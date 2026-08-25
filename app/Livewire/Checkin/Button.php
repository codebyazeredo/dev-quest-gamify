<?php

namespace App\Livewire\Checkin;

use App\Models\DailyCheckin;
use App\Services\CheckinService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Button extends Component
{
    public function checkIn(): void
    {
        app(CheckinService::class)->checkIn(auth()->user());

        $this->dispatch('checked-in');
    }

    public function render(): View
    {
        $user = auth()->user();

        $checkedInToday = DailyCheckin::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->exists();

        return view('livewire.checkin.button', [
            'checkedInToday' => $checkedInToday,
            'currentStreak' => app(CheckinService::class)->currentStreakFor($user),
        ]);
    }
}
