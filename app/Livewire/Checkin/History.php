<?php

namespace App\Livewire\Checkin;

use App\Models\DailyCheckin;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class History extends Component
{
    public string $month;

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function previousMonth(): void
    {
        $this->month = Carbon::createFromFormat('Y-m-d', $this->month.'-01')->subMonthNoOverflow()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = Carbon::createFromFormat('Y-m-d', $this->month.'-01')->addMonthNoOverflow()->format('Y-m');
    }

    public function goToCurrentMonth(): void
    {
        $this->month = now()->format('Y-m');
    }

    #[On('checked-in')]
    public function refreshCalendar(): void
    {
        // no-op: Livewire re-renders this component after any listener call, which is all we need
    }

    public function render(): View
    {
        $user = auth()->user();
        $monthStart = Carbon::createFromFormat('Y-m-d', $this->month.'-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $gridStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $monthEnd->copy()->endOfWeek(Carbon::MONDAY);

        $checkedInDates = DailyCheckin::where('user_id', $user->id)
            ->whereBetween('date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->pluck('date')
            ->map(fn ($date) => $date->toDateString())
            ->all();

        $days = collect();
        $cursor = $gridStart->copy();

        while ($cursor->lte($gridEnd)) {
            $days->push([
                'date' => $cursor->copy(),
                'inMonth' => $cursor->month === $monthStart->month,
                'isToday' => $cursor->isToday(),
                'isFuture' => $cursor->isFuture(),
                'checkedIn' => in_array($cursor->toDateString(), $checkedInDates, true),
            ]);
            $cursor->addDay();
        }

        return view('livewire.checkin.history', [
            'days' => $days,
            'monthLabel' => ucfirst($monthStart->locale('pt_BR')->translatedFormat('F \d\e Y')),
        ]);
    }
}
