<?php

namespace App\Http\Requests;

use App\Support\AnimalRegras;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * Validação de cadastro/edição de Animal. Usada nos dois fluxos (POST add e
 * PUT update) porque as regras são idênticas — mesmo padrão de LoteRequest.
 */
class AnimalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('usuario_id');
    }

    /**
     * Sem sessão, redireciona pro login em vez do 403 cru padrão do
     * FormRequest — mesmo comportamento do resto do sistema.
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(redirect()->route('login.index'));
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('peso')) {
            $this->merge([
                'peso' => str_replace(',', '.', (string) $this->input('peso')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],

            'identificador' => ['nullable', 'string', 'max:100'],

            // O lote precisa existir E pertencer (via fazenda) ao usuário
            // logado — sem isso, bastava enviar o id de um lote alheio pra
            // prender um animal a outra conta.
            'lote_id' => [
                'required',
                Rule::exists('lotes', 'id')->where(function ($query) {
                    $query->whereIn('fazenda_id', function ($sub) {
                        $sub->select('id')
                            ->from('fazendas')
                            ->where('cadastro_id', session('usuario_id'));
                    });
                }),
            ],

            'sexo' => ['required', Rule::in(AnimalRegras::SEXOS)],

            'raca' => ['nullable', 'string', 'max:100'],

            'data_nascimento' => ['required', 'date', 'before_or_equal:today'],

            'peso' => ['nullable', 'numeric', 'gt:0'],

            'status' => ['nullable', Rule::in(AnimalRegras::STATUS)],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Informe o nome/identificação do animal.',
            'lote_id.required' => 'Selecione o lote do animal.',
            'lote_id.exists' => 'Lote inválido — selecione um dos seus lotes.',
            'sexo.required' => 'Selecione o sexo do animal.',
            'sexo.in' => 'Sexo inválido.',
            'data_nascimento.required' => 'Informe a data de nascimento.',
            'data_nascimento.date' => 'Data de nascimento inválida.',
            'data_nascimento.before_or_equal' => 'A data de nascimento não pode ser no futuro.',
            'peso.numeric' => 'O peso deve ser um número (use apenas dígitos, ponto ou vírgula).',
            'peso.gt' => 'O peso deve ser maior que zero.',
            'status.in' => 'Status inválido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'lote_id' => 'lote',
            'data_nascimento' => 'data de nascimento',
        ];
    }
}
