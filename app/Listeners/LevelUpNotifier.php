<?php

namespace App\Listeners;

use App\Events\LevelUp;
use App\Support\ToastCollector;

class LevelUpNotifier
{
    public function __construct(private ToastCollector $toasts) {}

    public function handle(LevelUp $event): void
    {
        if (auth()->id() !== $event->user->id) {
            return;
        }

        $this->toasts->push(
            'level_up',
            'SUBIU DE NÍVEL!',
            "Você alcançou o Nível {$event->newLevel->level}.",
        );
    }
}
