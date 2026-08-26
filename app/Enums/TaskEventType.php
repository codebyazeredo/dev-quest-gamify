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
    case CREATION_COMPLETED = 9;

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
            self::CREATION_COMPLETED => 'Tarefa criada concluída',
        };
    }

    /**
     * APPROVED (testador) and CREATION_COMPLETED (criador do backlog) are
     * granted as a % of the task's own xpValue() instead of a flat amount —
     * see TaskService::grantDeferredTesterXp()/grantDeferredCreatorXp() —
     * so their reward scales with the task's value like the assignee's does,
     * instead of being flat regardless of how big/critical the task is.
     */
    public function isPercentageBased(): bool
    {
        return match ($this) {
            self::APPROVED, self::CREATION_COMPLETED => true,
            default => false,
        };
    }
}
