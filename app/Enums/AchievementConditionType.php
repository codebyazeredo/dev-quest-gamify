<?php

namespace App\Enums;

enum AchievementConditionType: int
{
    case BUGS_RESOLVED = 1;
    case DEPLOYS_MADE = 2;
    case TASKS_COMPLETED_IN_A_DAY = 3;
    case TASKS_COMPLETED_TOTAL = 4;
    case CHALLENGES_COMPLETED = 5;
    case CHECKINS_TOTAL = 6;

    public function label(): string
    {
        return match ($this) {
            self::BUGS_RESOLVED => 'Bugs resolvidos',
            self::DEPLOYS_MADE => 'Deploys realizados',
            self::TASKS_COMPLETED_IN_A_DAY => 'Tarefas concluídas em um dia',
            self::TASKS_COMPLETED_TOTAL => 'Tarefas concluídas',
            self::CHALLENGES_COMPLETED => 'Desafios concluídos',
            self::CHECKINS_TOTAL => 'Check-ins realizados',
        };
    }
}
