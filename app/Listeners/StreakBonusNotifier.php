<?php

namespace App\Listeners;

use App\Events\StreakBonusEarned;
use App\Support\ToastCollector;

class StreakBonusNotifier
{
    public function __construct(private ToastCollector $toasts) {}

    public function handle(StreakBonusEarned $event): void
    {
        $this->toasts->push(
            'streak',
            'Streak!',
            "You completed {$event->streakCount} consecutive days. +{$event->xpAwarded} XP",
        );
    }
}
