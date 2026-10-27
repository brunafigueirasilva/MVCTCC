<?php

namespace App\Http\Requests;

use App\Models\Cocho;
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
        if (!session()->has('usuario_id')) {
            return false;
        }

        // Edição: lote de outra conta (ou inexistente) é 404 ANTES da
        // validação — senão os erros de campo revelariam que o id existe.
        $id = $this->route('id');
        if ($id !== null) {
            $donoDoLote = \App\Models\Lote::whereKey($id)
                ->whereHas('fazenda', fn ($q) => $q->where('cadastro_id', session('usuario_id')))
                ->exists();
            abort_unless($donoDoLote, 404);
        }

        return true;
    }

    /**
     * Sem sessão, o padrão do FormRequest seria abortar com 403 cru — todo
     * o resto do sistema, ao perder a sessão, redireciona pra tela de
     * login (ver exigirLogin() nos outros Controllers). Mantém o mesmo
     * comportamento aqui em vez de uma página de erro genérica. Na API
     * (JSON) responde 401, que é o que o app espera pra voltar ao login.
     */
    protected function failedAuthorization()
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Não autenticado.',
            ], 401));
        }

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

        $capacidade = str_replace(',', '.', (string) $this->input('cocho_capacidade', ''));
        if ($capacidade === '' || (is_numeric($capacidade) && (float) $capacidade <= 0)) {
            $this->merge(['cocho_capacidade' => Cocho::CAPACIDADE_PADRAO]);
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

            // Texto livre e opcional (ex.: "Nelore", "Holandês", "Duroc") —
            // a raça varia demais entre espécies pra virar uma lista fixa
            // como tipo_animal/finalidade/idade_media.
            'raca' => ['nullable', 'string', 'max:100'],

            'sexo' => ['required', Rule::in(LoteRegras::SEXOS)],

            // A compatibilidade real (tipo de animal + sexo) é checada no
            // withValidator() abaixo, com uma mensagem explicando o motivo
            // — um "in" aqui só daria o genérico "valor inválido".
            'finalidade' => ['required', 'string'],

            'idade_media' => ['required', Rule::in(LoteRegras::IDADES_MEDIAS)],

            'quantidade_animais' => ['required', 'integer', 'min:1', 'max:100000'],

            // Obrigatório (0 = sem cocho nenhum ainda é uma resposta válida,
            // só não pode vir em branco) — LoteController::sincronizarCochos()
            // cria/remove os cochos automaticamente pra bater com este
            // número, então não existe cadastro manual de cocho individual
            // mais.
            'quantidade_cochos' => ['required', 'integer', 'min:0', 'max:100'],

            // Capacidade obrigatória — só vale pros cochos CRIADOS agora por
            // sincronizarCochos() (quantidade_cochos aumentando); cochos que
            // já existiam mantêm a capacidade/nível que já tinham, mesmo que
            // este campo venha preenchido aqui numa edição. Nível atual
            // continua opcional (sem valor, os cochos novos nascem com
            // nível 0, pra abastecer depois). Mesmas regras usadas na edição
            // individual (CochoController::update).
            // Opcional: vazio ou 0 (o app Flutter envia 0 por padrão) vira a
            // capacidade padrão do sistema, Cocho::CAPACIDADE_PADRAO (100 kg)
            // — ver prepareForValidation().
            'cocho_capacidade' => ['required', 'numeric', 'gt:0'],
            'cocho_nivel_atual' => ['nullable', 'numeric', 'min:0', 'lte:cocho_capacidade'],

            'peso_medio' => ['required', 'numeric', 'gt:0'],

            // Quando o lote entrou na fazenda (opcional; padrão: hoje).
            'data_entrada' => ['nullable', 'date', 'before_or_equal:today'],

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
            'quantidade_cochos.required' => 'Informe a quantidade de cochos (0 se este lote ainda não tiver nenhum).',
            'quantidade_cochos.integer' => 'A quantidade de cochos deve ser um número inteiro.',
            'quantidade_cochos.min' => 'A quantidade de cochos não pode ser negativa.',
            'quantidade_cochos.max' => 'Quantidade de cochos muito alta — confira o valor informado.',
            'cocho_capacidade.required' => 'Informe a capacidade dos cochos.',
            'cocho_capacidade.numeric' => 'A capacidade dos cochos deve ser um número.',
            'cocho_capacidade.gt' => 'A capacidade dos cochos deve ser maior que zero.',
            'cocho_nivel_atual.numeric' => 'O nível atual dos cochos deve ser um número.',
            'cocho_nivel_atual.min' => 'O nível atual dos cochos não pode ser negativo.',
            'cocho_nivel_atual.lte' => 'O nível atual dos cochos não pode ser maior que a capacidade informada.',
            'peso_medio.required' => 'Informe o peso médio do lote.',
            'peso_medio.numeric' => 'O peso médio deve ser um número (use apenas dígitos, ponto ou vírgula).',
            'peso_medio.gt' => 'O peso médio deve ser maior que zero.',
            'status.in' => 'Status inválido.',
            'data_entrada.date' => 'Data de entrada inválida.',
            'data_entrada.before_or_equal' => 'A data de entrada não pode estar no futuro.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome_lote' => 'nome do lote',
            'fazenda_id' => 'fazenda',
            'tipo_animal' => 'tipo de animal',
            'raca' => 'raça',
            'idade_media' => 'idade média',
            'quantidade_animais' => 'quantidade de animais',
            'quantidade_cochos' => 'quantidade de cochos',
            'cocho_capacidade' => 'capacidade dos cochos',
            'cocho_nivel_atual' => 'nível atual dos cochos',
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
