<?php

namespace App\Enums;

enum ChallengeType: int
{
    case TASKS_COMPLETED = 1;
    case BUGS_RESOLVED = 2;
    case DEPLOYS_MADE = 3;
    case CHECKINS_MADE = 4;

    public function label(): string
    {
        return match ($this) {
            self::TASKS_COMPLETED => 'Tarefas concluídas',
            self::BUGS_RESOLVED => 'Bugs resolvidos',
            self::DEPLOYS_MADE => 'Deploys realizados',
            self::CHECKINS_MADE => 'Check-ins diários',
        };
    }
}
