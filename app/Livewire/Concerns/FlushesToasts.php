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

    protected function toastSuccess(string $title, string $message = ''): void
    {
        app(ToastCollector::class)->push('success', $title, $message);
    }

    protected function toastError(string $title, string $message = ''): void
    {
        app(ToastCollector::class)->push('error', $title, $message);
    }
}
