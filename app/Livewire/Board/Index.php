<?php

namespace App\Livewire\Board;

use App\Models\Board;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public bool $showCreateModal = false;

    public function toggleCreate(): void
    {
        $this->authorize('create', Board::class);

        $this->showCreateModal = ! $this->showCreateModal;
    }

    #[On('close-modal')]
    public function closeModal(): void
    {
        $this->showCreateModal = false;
    }

    public function render(): View
    {
        $boards = Board::query()
            ->when(! auth()->user()->isAdmin(), fn ($query) => $query->where('is_active', true))
            ->withCount('tasks')
            ->orderBy('name')
            ->get();

        return view('livewire.board.index', ['boards' => $boards]);
    }
}
