<?php

namespace App\Livewire\Checkin;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\DailyCheckin;
use App\Services\CheckinService;
use App\Support\ToastCollector;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Button extends Component
{
    use FlushesToasts;

    public function checkIn(): void
    {
        $alreadyCheckedInToday = DailyCheckin::where('user_id', auth()->id())
            ->where('date', now()->toDateString())
            ->exists();

        app(CheckinService::class)->checkIn(auth()->user());

        if (! $alreadyCheckedInToday) {
            app(ToastCollector::class)->push('checkin', 'Check-in realizado!', 'Você ganhou XP só por aparecer hoje. Volte amanhã para manter a sequência.');
        }

        $this->dispatch('checked-in');
        $this->flushToasts();
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
