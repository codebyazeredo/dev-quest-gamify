<?php

namespace App\Enums;

enum TaskPriority: int
{
    case LOW = 1;
    case NORMAL = 2;
    case HIGH = 3;
    case CRITICAL = 4;

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::NORMAL => 'Normal',
            self::HIGH => 'High',
            self::CRITICAL => 'Critical',
        };
    }

    public function multiplier(): float
    {
        return match ($this) {
            self::LOW => 1.00,
            self::NORMAL => 1.50,
            self::HIGH => 2.00,
            self::CRITICAL => 5.00,
        };
    }
}
