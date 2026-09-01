<?php

namespace App\Http\Requests;

use App\Models\Cadastro;
use App\Rules\CpfValido;
use App\Rules\SenhaAtualCorreta;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;

/**
 * Validação da edição de perfil. Concentra aqui as regras de segurança
 * exigidas para qualquer alteração de dados sensíveis (e-mail, CPF, senha,
 * foto): reautenticação obrigatória, CPF só para maiores de idade e senha
 * forte quando trocada.
 */
class AtualizarPerfilRequest extends FormRequest
{
    protected $errorBag = 'perfil';

    private ?Cadastro $cadastro = null;

    /**
     * O acesso já é controlado por sessão (não há autenticação via Auth
     * facade neste projeto): só autoriza se existir um usuário logado.
     */
    public function authorize(): bool
    {
        return session()->has('usuario_id') && $this->cadastroLogado() !== null;
    }

    public function cadastroLogado(): ?Cadastro
    {
        if ($this->cadastro === null) {
            $this->cadastro = Cadastro::find(session('usuario_id'));
        }

        return $this->cadastro;
    }

    /**
     * Normaliza CPF e telefone para dígitos antes de validar/salvar, para
     * que a máscara digitada no front-end não afete unicidade nem o
     * cálculo do dígito verificador do CPF.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => $this->filled('cpf') ? preg_replace('/\D/', '', (string) $this->input('cpf')) : null,
            'telefone' => preg_replace('/\D/', '', (string) $this->input('telefone')),
        ]);
    }

    public function rules(): array
    {
        $cadastro = $this->cadastroLogado();
        $cadastroId = $cadastro?->id;

        return [
            'senha_atual' => ['required', 'string', new SenhaAtualCorreta($cadastro)],

            'responsavel' => ['required', 'string', 'max:255'],

            'email' => ['required', 'email', 'max:255', 'unique:cadastros,email,' . $cadastroId],

            'telefone' => ['required', 'digits_between:10,11'],

            'cpf' => ['nullable', 'digits:11', new CpfValido(), 'unique:cadastros,cpf,' . $cadastroId],

            // Só é exigida quando um CPF é informado: é a comprovação de
            // maioridade exigida para manter esse dado cadastrado.
            'data_nascimento' => [
                $this->filled('cpf') ? 'required' : 'nullable',
                'date',
                'before:today',
            ],

            'password' => [
                'nullable',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*@).{8,}$/',
            ],

            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'telefone.digits_between' => 'Informe um telefone válido com DDD (10 ou 11 dígitos).',
            'cpf.digits' => 'O CPF deve conter 11 dígitos.',
            'data_nascimento.required' => 'Informe a data de nascimento para cadastrar um CPF.',
            'data_nascimento.before' => 'Informe uma data de nascimento válida.',
            'password.confirmed' => 'A confirmação da nova senha não corresponde.',
            'password.regex' => 'A senha deve ter no mínimo 8 caracteres e incluir letra maiúscula, letra minúscula, número e o caractere @.',
        ];
    }

    public function attributes(): array
    {
        return [
            'senha_atual' => 'senha atual',
            'responsavel' => 'nome',
            'data_nascimento' => 'data de nascimento',
            'password' => 'nova senha',
        ];
    }

    /**
     * Regras que dependem de outros campos já validados (idade mínima e
     * senha nova diferente da atual) entram aqui, depois da validação
     * "de forma", para poder reaproveitar os valores já normalizados.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->filled('cpf') && $this->filled('data_nascimento')) {
                $idade = Carbon::parse($this->input('data_nascimento'))->age;

                if ($idade < 18) {
                    $validator->errors()->add(
                        'data_nascimento',
                        'Só é permitido cadastrar CPF para maiores de 18 anos.'
                    );
                }
            }

            $cadastro = $this->cadastroLogado();

            if ($cadastro && $this->filled('password') && Hash::check($this->input('password'), $cadastro->senha)) {
                $validator->errors()->add('password', 'A nova senha deve ser diferente da senha atual.');
            }
        });
    }
}
