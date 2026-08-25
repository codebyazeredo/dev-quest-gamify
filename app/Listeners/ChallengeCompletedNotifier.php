<?php

namespace App\Listeners;

use App\Events\ChallengeCompleted;
use App\Support\ToastCollector;

class ChallengeCompletedNotifier
{
    public function __construct(private ToastCollector $toasts) {}

    public function handle(ChallengeCompleted $event): void
    {
        $this->toasts->push(
            'challenge',
            'Challenge completed!',
            $event->challenge->name,
        );
    }
}
