<?php

namespace App\Livewire\Gamification;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Ranking extends Component
{
    use WithPagination;

    #[Url]
    public string $activeRole = 'dev';

    public function setRole(string $role): void
    {
        $this->activeRole = $role;
        $this->resetPage();
    }

    public function render(): View
    {
        $users = User::query()
            ->role($this->activeRole)
            ->withSum('xpTransactions as total_xp', 'amount')
            ->orderByDesc('total_xp')
            ->paginate(20);

        return view('livewire.gamification.ranking', ['users' => $users]);
    }
}
