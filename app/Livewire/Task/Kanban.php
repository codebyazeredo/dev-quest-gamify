<?php

namespace App\Livewire\Task;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\Board;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Kanban extends Component
{
    use FlushesToasts;

    public Board $board;

    public ?int $creatingInColumnId = null;

    public ?int $editingTaskId = null;

    public function mount(Board $board): void
    {
        $this->board = $board;

        $this->loadBoard();
    }

    protected function loadBoard(): void
    {
        $this->board->load(['columns.tasks.category', 'columns.tasks.assignedTo']);
    }

    public function openCreate(int $columnId): void
    {
        $this->authorize('create', Task::class);

        $this->creatingInColumnId = $columnId;
    }

    #[On('open-task-edit')]
    public function openEdit(int $taskId): void
    {
        $this->editingTaskId = $taskId;
    }

    #[On('task-created')]
    #[On('task-saved')]
    public function taskSaved(): void
    {
        $this->creatingInColumnId = null;
        $this->editingTaskId = null;
        $this->loadBoard();
    }

    #[On('close-modal')]
    public function closeModal(): void
    {
        $this->creatingInColumnId = null;
        $this->editingTaskId = null;
    }

    public function moveTask(int $taskId, int $columnId, int $position): void
    {
        $task = Task::findOrFail($taskId);
        $column = $this->board->columns->firstWhere('id', $columnId);

        if (! $column) {
            abort(404);
        }

        $this->authorize('move', [$task, $column]);

        app(TaskService::class)->move($task, $column, $position, auth()->user());

        $this->loadBoard();
        $this->flushToasts();
    }

    public function claim(int $taskId): void
    {
        $task = Task::findOrFail($taskId);

        $this->authorize('claim', $task);

        app(TaskService::class)->assign($task, auth()->user());

        $this->loadBoard();
        $this->flushToasts();
    }

    public function render(): View
    {
        return view('livewire.task.kanban');
    }
}
