<?php

namespace App\Livewire\Gamification;

use App\Livewire\Concerns\WithAdjustablePerPage;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Ranking extends Component
{
    use WithAdjustablePerPage;
    use WithPagination;

    #[Url]
    public string $activeRole = 'dev';

    #[Url]
    public string $period = 'total';

    public function setRole(string $role): void
    {
        $this->activeRole = $role;
        $this->resetPage();
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        $this->resetPage();
    }

    private function periodStart(): ?CarbonInterface
    {
        return match ($this->period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => null,
        };
    }

    public function render(): View
    {
        $periodStart = $this->periodStart();

        $users = User::query()
            ->role($this->activeRole)
            ->withSum(['xpTransactions as total_xp' => function ($query) use ($periodStart) {
                if ($periodStart !== null) {
                    $query->where('created_at', '>=', $periodStart);
                }
            }], 'amount')
            ->orderByDesc('total_xp')
            ->paginate($this->perPage);

        return view('livewire.gamification.ranking', ['users' => $users]);
    }
}
