<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Support\ToastCollector;

class AchievementUnlockedNotifier
{
    public function __construct(private ToastCollector $toasts) {}

    public function handle(AchievementUnlocked $event): void
    {
        if (auth()->id() !== $event->user->id) {
            return;
        }

        $this->toasts->push(
            'achievement',
            'Conquista desbloqueada!',
            $event->achievement->name,
        );
    }
}
