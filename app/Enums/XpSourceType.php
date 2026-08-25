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
            self::TASK => 'Conclusão de tarefa',
            self::TASK_EVENT => 'Evento de tarefa',
            self::ACHIEVEMENT => 'Conquista',
            self::CHECKIN => 'Check-in',
            self::CHALLENGE => 'Desafio',
            self::BONUS => 'Bônus',
            self::PENALTY => 'Penalidade',
            self::ADMIN_ADJUSTMENT => 'Ajuste administrativo',
        };
    }
}
