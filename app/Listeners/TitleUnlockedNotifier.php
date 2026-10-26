<?php

namespace App\Listeners;

use App\Events\TitleUnlocked;
use App\Support\ToastCollector;

class TitleUnlockedNotifier
{
    public function __construct(private ToastCollector $toasts) {}

    public function handle(TitleUnlocked $event): void
    {
        if (auth()->id() !== $event->user->id) {
            return;
        }

        $this->toasts->push('achievement', 'Novo título desbloqueado!', $event->title->name);
    }
}
