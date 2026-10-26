<?php

namespace App\Enums;

enum TitleUnlockType: string
{
    case ACHIEVEMENT = 'achievement';
    case CHALLENGE = 'challenge';
    case CHALLENGES_COUNT = 'challenges_count';

    public function label(): string
    {
        return match ($this) {
            self::ACHIEVEMENT => 'Ao desbloquear uma conquista',
            self::CHALLENGE => 'Ao concluir um desafio específico',
            self::CHALLENGES_COUNT => 'Ao concluir uma quantidade de desafios',
        };
    }
}
