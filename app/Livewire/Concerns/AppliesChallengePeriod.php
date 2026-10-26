<?php

namespace App\Livewire\Concerns;

trait AppliesChallengePeriod
{
    public function applyPeriod(string $preset): void
    {
        [$start, $end] = match ($preset) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'next7' => [now(), now()->addDays(7)->endOfDay()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [null, null],
        };

        if ($start === null) {
            return;
        }

        $this->starts_at = $start->format('Y-m-d\TH:i');
        $this->ends_at = $end->format('Y-m-d\TH:i');
    }
}
