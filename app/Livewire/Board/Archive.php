<?php

namespace App\Livewire\Board;

use App\Models\Board;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Archive extends Component
{
    public Board $board;

    public function mount(Board $board): void
    {
        $this->authorize('view', $board);

        $this->board = $board;
    }

    public function render(): View
    {
        $archived = Task::where('board_id', $this->board->id)
            ->whereNotNull('archived_at')
            ->with(['category', 'priority', 'assignedTo', 'taskEvents'])
            ->orderByDesc('archived_at')
            ->get();

        return view('livewire.board.archive', [
            'completed' => $archived->whereNotNull('completed_at')->values(),
            'notCompleted' => $archived->whereNull('completed_at')->values(),
        ]);
    }
}
