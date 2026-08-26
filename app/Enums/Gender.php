<?php

namespace App\Enums;

enum Gender: int
{
    case MALE = 1;
    case FEMALE = 2;
    case OTHER = 3;

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Masculino',
            self::FEMALE => 'Feminino',
            self::OTHER => 'Outro',
        };
    }
}
