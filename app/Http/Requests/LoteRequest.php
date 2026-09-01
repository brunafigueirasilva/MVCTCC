<?php

namespace App\Http\Requests;

use App\Support\LoteRegras;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validação de cadastro/edição de Lote. Usada nos dois fluxos (POST add e
 * PUT update) porque as regras são idênticas — a única diferença entre
 * criar e editar é o que o Controller faz com os dados já validados.
 *
 * Importante: nenhuma dessas regras confia no JavaScript da tela. O
 * front-end só filtra as opções pra melhorar a experiência (evitar erro
 * óbvio); quem garante que nada inválido chega no banco é este arquivo.
 */
class LoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('usuario_id');
    }

    /**
     * Sem sessão, o padrão do FormRequest seria abortar com 403 cru — todo
     * o resto do sistema, ao perder a sessão, redireciona pra tela de
     * login (ver exigirLogin() nos outros Controllers). Mantém o mesmo
     * comportamento aqui em vez de uma página de erro genérica.
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(redirect()->route('login.index'));
    }

    /**
     * CPF/telefone e afins normalmente seriam normalizados aqui; no caso do
     * lote, o único ajuste necessário é garantir que o peso aceite vírgula
     * decimal (comum em formulários em português) além do ponto.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('peso_medio')) {
            $this->merge([
                'peso_medio' => str_replace(',', '.', (string) $this->input('peso_medio')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            // Único por fazenda (não pelo usuário inteiro): duas fazendas
            // diferentes podem ter, cada uma, um lote chamado "Lote A" —
            // mesmo critério que "Registrar Doença" já usa pra achar/criar
            // o lote de isolamento (fazenda_id + nome_lote).
            'nome_lote' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lotes', 'nome_lote')
                    ->where(fn ($query) => $query->where('fazenda_id', $this->input('fazenda_id')))
                    ->ignore($this->route('id')),
            ],

            // A fazenda precisa existir E pertencer ao usuário logado —
            // sem isso, bastava enviar o id de uma fazenda alheia pra
            // prender um lote a outra conta.
            'fazenda_id' => [
                'required',
                Rule::exists('fazendas', 'id')->where('cadastro_id', session('usuario_id')),
            ],

            'tipo_animal' => ['required', Rule::in(LoteRegras::tiposAnimais())],
            'sexo' => ['required', Rule::in(LoteRegras::SEXOS)],

            // A compatibilidade real (tipo de animal + sexo) é checada no
            // withValidator() abaixo, com uma mensagem explicando o motivo
            // — um "in" aqui só daria o genérico "valor inválido".
            'finalidade' => ['required', 'string'],

            'idade_media' => ['required', Rule::in(LoteRegras::IDADES_MEDIAS)],

            'quantidade_animais' => ['required', 'integer', 'min:1', 'max:100000'],

            'peso_medio' => ['required', 'numeric', 'gt:0'],

            // "after_or_equal:today" usa a data atual do SERVIDOR (Carbon,
            // no fuso configurado em config/app.php) — nunca o relógio do
            // computador de quem está preenchendo o formulário.
            'data_entrada' => ['required', 'date', 'after_or_equal:today'],

            'status' => ['nullable', Rule::in(LoteRegras::STATUS)],
        ];
    }

    public function messages(): array
    {
        return [
            'nome_lote.required' => 'Informe um nome para o lote.',
            'nome_lote.unique' => 'Já existe um lote com esse nome nesta fazenda.',
            'fazenda_id.required' => 'Selecione a fazenda do lote.',
            'fazenda_id.exists' => 'Fazenda inválida — selecione uma das suas fazendas.',
            'tipo_animal.required' => 'Selecione o tipo de animal.',
            'tipo_animal.in' => 'Tipo de animal inválido.',
            'sexo.required' => 'Selecione o sexo do lote.',
            'sexo.in' => 'Sexo inválido.',
            'finalidade.required' => 'Selecione a finalidade do lote.',
            'idade_media.required' => 'Selecione a idade média do lote.',
            'idade_media.in' => 'Faixa de idade inválida.',
            'quantidade_animais.required' => 'Informe a quantidade de animais.',
            'quantidade_animais.integer' => 'A quantidade de animais deve ser um número inteiro.',
            'quantidade_animais.min' => 'A quantidade de animais deve ser de pelo menos 1.',
            'quantidade_animais.max' => 'Quantidade de animais muito alta — confira o valor informado.',
            'peso_medio.required' => 'Informe o peso médio do lote.',
            'peso_medio.numeric' => 'O peso médio deve ser um número (use apenas dígitos, ponto ou vírgula).',
            'peso_medio.gt' => 'O peso médio deve ser maior que zero.',
            'data_entrada.required' => 'Informe a data de entrada.',
            'data_entrada.date' => 'Data de entrada inválida.',
            'data_entrada.after_or_equal' => 'A data de cadastro não pode ser anterior à data atual.',
            'status.in' => 'Status inválido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome_lote' => 'nome do lote',
            'fazenda_id' => 'fazenda',
            'tipo_animal' => 'tipo de animal',
            'idade_media' => 'idade média',
            'quantidade_animais' => 'quantidade de animais',
            'peso_medio' => 'peso médio',
            'data_entrada' => 'data de entrada',
        ];
    }

    /**
     * Regras cruzadas (que dependem de mais de um campo ao mesmo tempo)
     * entram aqui, com mensagens explicando o motivo real da rejeição —
     * nunca um "valor inválido" genérico, como pede a regra de negócio.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $tipo = $this->input('tipo_animal');
            $sexo = $this->input('sexo');
            $finalidade = $this->input('finalidade');
            $peso = $this->input('peso_medio');

            if ($tipo && $finalidade && !$validator->errors()->has('finalidade')) {
                $permitidas = LoteRegras::finalidadesPermitidas($tipo, $sexo);

                if (!in_array($finalidade, $permitidas, true)) {
                    if ($sexo === 'Macho' && in_array($finalidade, LoteRegras::FINALIDADES_SO_FEMEA, true)) {
                        $mensagem = "Animais machos não podem ser cadastrados com a finalidade \"{$finalidade}\".";
                    } elseif (in_array($finalidade, LoteRegras::todasFinalidades(), true)) {
                        $mensagem = "Finalidade inválida para este tipo de animal. \"{$tipo}\" não possui a finalidade \"{$finalidade}\".";
                    } else {
                        $mensagem = "A finalidade \"{$finalidade}\" não é reconhecida pelo sistema.";
                    }

                    $validator->errors()->add('finalidade', $mensagem);
                }
            }

            if ($tipo && $peso !== null && is_numeric($peso) && !LoteRegras::pesoValido($tipo, $peso)) {
                $faixa = LoteRegras::faixaPeso($tipo);
                $validator->errors()->add(
                    'peso_medio',
                    "Peso fora da faixa esperada para \"{$tipo}\": o peso médio deve ficar entre {$faixa['min']} kg e {$faixa['max']} kg."
                );
            }
        });
    }
}
