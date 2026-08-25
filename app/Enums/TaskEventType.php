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

    public function label(): string
    {
        return match ($this) {
            self::STARTED => 'Started',
            self::DEVELOPMENT_COMPLETED => 'Development completed',
            self::REVIEW_COMPLETED => 'Review completed',
            self::TEST_COMPLETED => 'Test completed',
            self::HOMOLOGATION_COMPLETED => 'Homologation completed',
            self::DEPLOYED => 'Deploy',
            self::COMPLETED => 'Completed',
        };
    }
}
