<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Support\ToastCollector;

class AchievementUnlockedNotifier
{
    public function __construct(private ToastCollector $toasts) {}

    public function handle(AchievementUnlocked $event): void
    {
        $this->toasts->push(
            'achievement',
            'Achievement unlocked!',
            $event->achievement->name,
        );
    }
}
