<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case WAITING_CUSTOMER = 'waiting_customer';
    case RESOLVED = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Aberto',
            self::IN_PROGRESS => 'Em andamento',
            self::WAITING_CUSTOMER => 'Aguardando cliente',
            self::RESOLVED => 'Resolvido',
        };
    }

    public function portalLabel(): string
    {
        return $this === self::WAITING_CUSTOMER ? 'Aguardando sua resposta' : $this->label();
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'warning',
            self::IN_PROGRESS => 'primary',
            self::WAITING_CUSTOMER => 'info',
            self::RESOLVED => 'success',
        };
    }
}
