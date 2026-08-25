<?php

namespace App\Enums;

enum XpSourceType: int
{
    case TASK = 1;
    case TASK_EVENT = 2;
    case ACHIEVEMENT = 3;
    case CHECKIN = 4;
    case CHALLENGE = 5;
    case BONUS = 6;
    case PENALTY = 7;
    case ADMIN_ADJUSTMENT = 8;

    public function label(): string
    {
        return match ($this) {
            self::TASK => 'Task completion',
            self::TASK_EVENT => 'Task event',
            self::ACHIEVEMENT => 'Achievement',
            self::CHECKIN => 'Check-in',
            self::CHALLENGE => 'Challenge',
            self::BONUS => 'Bonus',
            self::PENALTY => 'Penalty',
            self::ADMIN_ADJUSTMENT => 'Admin adjustment',
        };
    }
}
