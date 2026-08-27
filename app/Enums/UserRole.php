<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum UserRole: string
{
    case ADMIN = 'admin';
    case PRODUCT_OWNER = 'product_owner';
    case DEVELOPER = 'dev';
    case TESTER = 'tester';
    case SUPORTE = 'suporte';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::PRODUCT_OWNER => 'Product Owner',
            self::DEVELOPER => 'Desenvolvedor',
            self::TESTER => 'Testes',
            self::SUPORTE => 'Suporte',
        };
    }

    public static function labelFor(string $roleName): string
    {
        $known = self::tryFrom($roleName);

        if ($known !== null) {
            return $known->label();
        }

        return Str::headline($roleName);
    }
}
