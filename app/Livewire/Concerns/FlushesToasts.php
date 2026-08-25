<?php

namespace App\Livewire\Concerns;

use App\Support\ToastCollector;

trait FlushesToasts
{
    protected function flushToasts(): void
    {
        foreach (app(ToastCollector::class)->flush() as $toast) {
            $this->dispatch('toast', toast: $toast);
        }
    }
}
