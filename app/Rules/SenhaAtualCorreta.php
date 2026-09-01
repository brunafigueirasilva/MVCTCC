<?php

namespace App\Rules;

use App\Models\Cadastro;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

/**
 * Exige que o usuário reconfirme a senha atual para poder salvar qualquer
 * alteração no próprio perfil (dados sensíveis: e-mail, CPF, senha, foto).
 * Evita que uma sessão esquecida aberta em outro dispositivo seja usada
 * por terceiros para sequestrar a conta.
 */
class SenhaAtualCorreta implements ValidationRule
{
    public function __construct(private readonly Cadastro $cadastro)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Hash::check((string) $value, $this->cadastro->senha)) {
            $fail('Senha atual incorreta. Por segurança, confirme sua senha para salvar as alterações.');
        }
    }
}
