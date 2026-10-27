<?php

namespace Tests\Unit\Rules;

use App\Rules\ValidCnpj;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ValidCnpjTest extends TestCase
{
    #[DataProvider('validCnpjs')]
    public function test_accepts_valid_cnpj_with_or_without_mask(string $cnpj): void
    {
        $this->assertTrue(ValidCnpj::isValid($cnpj));
    }

    #[DataProvider('invalidCnpjs')]
    public function test_rejects_invalid_cnpj(string $cnpj): void
    {
        $this->assertFalse(ValidCnpj::isValid($cnpj));
    }

    /** @return array<string, array<int, string>> */
    public static function validCnpjs(): array
    {
        return [
            'com máscara' => ['11.222.333/0001-81'],
            'só dígitos' => ['11222333000181'],
        ];
    }

    /** @return array<string, array<int, string>> */
    public static function invalidCnpjs(): array
    {
        return [
            'dígito verificador errado' => ['11.222.333/0001-82'],
            'dígitos repetidos' => ['11.111.111/1111-11'],
            'tamanho incorreto' => ['1122233300018'],
            'vazio' => [''],
        ];
    }
}
