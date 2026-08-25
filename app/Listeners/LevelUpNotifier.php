<?php

namespace App\Listeners;

use App\Events\LevelUp;
use App\Support\ToastCollector;

class LevelUpNotifier
{
    public function __construct(private ToastCollector $toasts) {}

    public function handle(LevelUp $event): void
    {
        $this->toasts->push(
            'level_up',
            'LEVEL UP!',
            "You reached Level {$event->newLevel->level}.",
        );
    }
}
