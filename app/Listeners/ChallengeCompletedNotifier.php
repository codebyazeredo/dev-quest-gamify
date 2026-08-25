<?php

namespace App\Listeners;

use App\Events\ChallengeCompleted;
use App\Support\ToastCollector;

class ChallengeCompletedNotifier
{
    public function __construct(private ToastCollector $toasts) {}

    public function handle(ChallengeCompleted $event): void
    {
        if (auth()->id() !== $event->user->id) {
            return;
        }

        $this->toasts->push(
            'challenge',
            'Desafio concluído!',
            $event->challenge->name,
        );
    }
}
