<?php

namespace App\Enums;

enum UserRole: int
{
    case ADMIN = 1;
    case PRODUCT_OWNER = 2;
    case DEVELOPER = 3;

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::PRODUCT_OWNER => 'Product Owner',
            self::DEVELOPER => 'Developer',
        };
    }
}
