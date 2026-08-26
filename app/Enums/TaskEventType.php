<?php

namespace App\Enums;

enum TaskEventType: int
{
    case STARTED = 1;
    case DEVELOPMENT_COMPLETED = 2;
    case REVIEW_COMPLETED = 3;
    case TEST_COMPLETED = 4;
    case HOMOLOGATION_COMPLETED = 5;
    case DEPLOYED = 6;
    case COMPLETED = 7;
    case APPROVED = 8;

    public function label(): string
    {
        return match ($this) {
            self::STARTED => 'Iniciado',
            self::DEVELOPMENT_COMPLETED => 'Desenvolvimento concluído',
            self::REVIEW_COMPLETED => 'Revisão concluída',
            self::TEST_COMPLETED => 'Teste concluído',
            self::HOMOLOGATION_COMPLETED => 'Homologação concluída',
            self::DEPLOYED => 'Implantado',
            self::COMPLETED => 'Concluído',
            self::APPROVED => 'Aprovado pelo testador',
        };
    }
}
