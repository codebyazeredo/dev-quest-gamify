<?php

namespace App\Livewire\Gamification;

use App\Models\XpTransaction;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class XpHistory extends Component
{
    use WithPagination;

    public ?int $limit = null;

    #[On('checked-in')]
    public function refresh(): void
    {
        // no-op: Livewire re-renders this component after any listener call
    }

    public function render(): View
    {
        $query = XpTransaction::where('user_id', auth()->id())->latest();

        if ($this->limit !== null) {
            return view('livewire.gamification.xp-history', [
                'transactions' => $query->limit($this->limit)->get(),
                'paginated' => false,
            ]);
        }

        return view('livewire.gamification.xp-history', [
            'transactions' => $query->paginate(15),
            'paginated' => true,
        ]);
    }
}
