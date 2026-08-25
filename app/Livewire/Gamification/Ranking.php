<?php

namespace App\Livewire\Gamification;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Ranking extends Component
{
    use WithPagination;

    public function render(): View
    {
        $users = User::query()
            ->withSum('xpTransactions as total_xp', 'amount')
            ->orderByDesc('total_xp')
            ->paginate(20);

        return view('livewire.gamification.ranking', ['users' => $users]);
    }
}
