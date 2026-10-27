<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCnpj implements ValidationRule
{
    private const WEIGHTS = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::isValid((string) $value)) {
            $fail('O campo :attribute não é um CNPJ válido.');
        }
    }

    public static function isValid(string $value): bool
    {
        $cnpj = preg_replace('/\D/', '', $value);

        if (! is_string($cnpj) || strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj) === 1) {
            return false;
        }

        $firstDigit = self::checkDigit(substr($cnpj, 0, 12), self::WEIGHTS);
        $secondDigit = self::checkDigit(substr($cnpj, 0, 12).$firstDigit, [6, ...self::WEIGHTS]);

        return $cnpj[12] === (string) $firstDigit && $cnpj[13] === (string) $secondDigit;
    }

    /** @param array<int, int> $weights */
    private static function checkDigit(string $digits, array $weights): int
    {
        $sum = 0;

        foreach ($weights as $position => $weight) {
            $sum += (int) $digits[$position] * $weight;
        }

        $remainder = $sum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
