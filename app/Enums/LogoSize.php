<?php

namespace App\Enums;

enum LogoSize: string
{
    case SMALL = 'small';
    case MEDIUM = 'medium';
    case LARGE = 'large';

    public function label(): string
    {
        return match ($this) {
            self::SMALL => 'Pequeno',
            self::MEDIUM => 'Médio',
            self::LARGE => 'Grande',
        };
    }
}
