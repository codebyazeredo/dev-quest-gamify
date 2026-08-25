<?php

namespace App\Listeners;

use App\Events\StreakBonusEarned;
use App\Support\ToastCollector;

class StreakBonusNotifier
{
    public function __construct(private ToastCollector $toasts) {}

    public function handle(StreakBonusEarned $event): void
    {
        if (auth()->id() !== $event->user->id) {
            return;
        }

        $this->toasts->push(
            'streak',
            'Sequência!',
            "Você completou {$event->streakCount} dias consecutivos. +{$event->xpAwarded} XP",
        );
    }
}
