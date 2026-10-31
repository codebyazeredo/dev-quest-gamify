<?php

namespace App\Enums;

enum LogoSize: string
{
    case SMALL = 'small';
    case MEDIUM = 'medium';
    case LARGE = 'large';

    /** Altura da logo nas telas de acesso, em rem. */
    public function loginHeightRem(): int
    {
        return match ($this) {
            self::SMALL => 4,
            self::MEDIUM => 6,
            self::LARGE => 8,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::SMALL => 'Pequeno',
            self::MEDIUM => 'Médio',
            self::LARGE => 'Grande',
        };
    }
}
