<?php

namespace App\Livewire\Task;

use App\Enums\TaskStatus;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Services\TaskService;
use App\Support\ToastCollector;
use Illuminate\Auth\Access\AuthorizationException;
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
        $this->board->load([
            'columns.tasks' => fn ($query) => $query->visibleTo(auth()->user())->with(['category', 'assignedTo', 'taskEvents']),
        ]);
    }

    public function openCreate(int $columnId): void
    {
        $this->authorize('create', Task::class);

        $column = $this->board->columns->firstWhere('id', $columnId);

        if (! $column || $column->status !== TaskStatus::BACKLOG) {
            abort(404);
        }

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

        try {
            $this->authorize('move', [$task, $column]);
        } catch (AuthorizationException) {
            app(ToastCollector::class)->push('error', 'Movimento não permitido', $this->moveDenialMessage($task, $column));
            $this->flushToasts();

            return;
        }

        app(TaskService::class)->move($task, $column, $position, auth()->user());

        $this->toastSuccess('Tarefa movida', "\"{$task->title}\" foi movida para {$column->name}.");
        $this->loadBoard();
        $this->flushToasts();
    }

    private function moveDenialMessage(Task $task, BoardColumn $column): string
    {
        if ($column->status === TaskStatus::APPROVED) {
            return 'A coluna "Aprovado" só é alcançada aprovando a tarefa em teste.';
        }

        if ($column->status === TaskStatus::DONE) {
            return 'A coluna "Concluído" só é alcançada marcando homologação e implantação na tarefa aprovada.';
        }

        if ($task->status === TaskStatus::TESTING) {
            return 'Tarefas em teste só saem por aprovação ou reprovação do testador.';
        }

        return 'Você não tem permissão para mover esta tarefa para esta coluna.';
    }

    public function claim(int $taskId): void
    {
        $task = Task::findOrFail($taskId);

        $this->authorize('claim', $task);

        app(TaskService::class)->assign($task, auth()->user());

        $this->toastSuccess('Tarefa assumida', 'Você agora é o responsável por esta tarefa.');
        $this->loadBoard();
        $this->flushToasts();
    }

    public function render(): View
    {
        return view('livewire.task.kanban');
    }
}
