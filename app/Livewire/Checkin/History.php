<?php

namespace App\Livewire\Checkin;

use App\Models\DailyCheckin;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class History extends Component
{
    #[On('checked-in')]
    public function refreshCalendar(): void
    {
        // no-op: Livewire re-renders this component after any listener call, which is all we need
    }

    public function render(): View
    {
        $user = auth()->user();

        $checkedInDates = DailyCheckin::where('user_id', $user->id)
            ->where('date', '>=', now()->subDays(13)->toDateString())
            ->pluck('date')
            ->map(fn ($date) => $date->toDateString())
            ->all();

        $days = collect(range(13, 0))
            ->map(fn (int $offset) => now()->subDays($offset))
            ->map(fn ($date) => [
                'date' => $date,
                'checkedIn' => in_array($date->toDateString(), $checkedInDates, true),
            ]);

        return view('livewire.checkin.history', ['days' => $days]);
    }
}
