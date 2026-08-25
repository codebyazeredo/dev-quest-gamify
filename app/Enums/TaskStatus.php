<?php

namespace App\Enums;

enum TaskStatus: int
{
    case BACKLOG = 1;
    case TODO = 2;
    case DOING = 3;
    case REVIEW = 4;
    case TESTING = 5;
    case DONE = 6;

    public function label(): string
    {
        return match ($this) {
            self::BACKLOG => 'Backlog',
            self::TODO => 'To Do',
            self::DOING => 'Doing',
            self::REVIEW => 'Review',
            self::TESTING => 'Testing',
            self::DONE => 'Done',
        };
    }
}
