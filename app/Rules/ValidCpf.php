<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cpf = preg_replace('/\D/', '', (string) $value);

        if (! is_string($cpf) || strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf) === 1) {
            $fail('O campo :attribute não é um CPF válido.');

            return;
        }

        for ($position = 9; $position <= 10; $position++) {
            $sum = 0;

            for ($i = 0; $i < $position; $i++) {
                $sum += (int) $cpf[$i] * (($position + 1) - $i);
            }

            $checkDigit = ($sum * 10) % 11;
            $checkDigit = $checkDigit === 10 ? 0 : $checkDigit;

            if ($checkDigit !== (int) $cpf[$position]) {
                $fail('O campo :attribute não é um CPF válido.');

                return;
            }
        }
    }
}
