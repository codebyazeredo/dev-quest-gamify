<?php

namespace App\Enums;

enum ChallengeType: int
{
    case TASKS_COMPLETED = 1;
    case BUGS_RESOLVED = 2;

    public function label(): string
    {
        return match ($this) {
            self::TASKS_COMPLETED => 'Tasks completed',
            self::BUGS_RESOLVED => 'Bugs resolved',
        };
    }
}
