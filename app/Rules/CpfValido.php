<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida um CPF pelo algoritmo oficial de dígitos verificadores (módulo 11),
 * não apenas pela máscara. Aceita o valor com ou sem pontuação.
 */
class CpfValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cpf = preg_replace('/\D/', '', (string) $value);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            $fail('O :attribute informado não é válido.');

            return;
        }

        for ($posicao = 9; $posicao < 11; $posicao++) {
            $soma = 0;

            for ($indice = 0; $indice < $posicao; $indice++) {
                $soma += (int) $cpf[$indice] * (($posicao + 1) - $indice);
            }

            $digitoVerificador = ((10 * $soma) % 11) % 10;

            if ((int) $cpf[$posicao] !== $digitoVerificador) {
                $fail('O :attribute informado não é válido.');

                return;
            }
        }
    }
}
