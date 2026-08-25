<?php

namespace App\Livewire\Board;

use App\Models\Board;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    public Board $board;

    public bool $showEditModal = false;

    public function mount(Board $board): void
    {
        $this->authorize('view', $board);

        $this->board = $board;
    }

    public function toggleEdit(): void
    {
        $this->authorize('update', $this->board);

        $this->showEditModal = ! $this->showEditModal;
    }

    #[On('board-updated')]
    public function boardUpdated(): void
    {
        $this->board->refresh();
        $this->showEditModal = false;
    }

    #[On('close-modal')]
    public function closeModal(): void
    {
        $this->showEditModal = false;
    }

    public function render(): View
    {
        return view('livewire.board.show');
    }
}
