<?php

namespace App\Enums;

enum PullRequestState: string
{
    case OPEN = 'open';
    case MERGED = 'merged';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'PR aberto',
            self::MERGED => 'PR mergeado',
            self::CLOSED => 'PR fechado',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::OPEN => 'success',
            self::MERGED => 'primary',
            self::CLOSED => 'secondary',
        };
    }
}
