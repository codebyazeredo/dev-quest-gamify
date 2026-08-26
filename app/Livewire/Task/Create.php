<?php

namespace App\Livewire\Task;

use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskPriority;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    public Board $board;

    public int $columnId;

    public string $title = '';

    public string $description = '';

    public ?int $category_id = null;

    public ?int $priority_id = null;

    public ?int $assigned_to = null;

    public ?string $due_at = null;

    public function mount(Board $board, int $columnId): void
    {
        $this->authorize('create', Task::class);

        $column = BoardColumn::findOrFail($columnId);

        if ($column->board_id !== $board->id || $column->status !== TaskStatus::BACKLOG) {
            abort(404);
        }

        $this->board = $board;
        $this->columnId = $columnId;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:task_categories,id'],
            'priority_id' => ['required', 'exists:task_priorities,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'due_at' => ['nullable', 'date'],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', Task::class);

        $validated = $this->validate();
        $validated['column_id'] = $this->columnId;

        if (! (auth()->user()->isAdmin() || auth()->user()->isProductOwner())) {
            $validated['assigned_to'] = null;
        }

        app(TaskService::class)->create($validated, auth()->user());

        $this->dispatch('task-created');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.task.create', [
            'categories' => TaskCategory::orderBy('name')->get(),
            'priorities' => TaskPriority::orderBy('multiplier')->get(),
            'developers' => (auth()->user()->isAdmin() || auth()->user()->isProductOwner())
                ? User::orderBy('name')->get()
                : collect(),
        ]);
    }
}
