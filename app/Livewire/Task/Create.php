<?php

namespace App\Livewire\Task;

use App\Enums\TaskPriority;
use App\Models\Board;
use App\Models\Task;
use App\Models\TaskCategory;
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

    public int $priority = 2;

    public ?int $assigned_to = null;

    public ?int $estimated_points = null;

    public function mount(Board $board, int $columnId): void
    {
        $this->authorize('create', Task::class);

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
            'priority' => ['required', 'integer'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'estimated_points' => ['nullable', 'integer', 'min:0'],
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
            'priorities' => TaskPriority::cases(),
            'developers' => (auth()->user()->isAdmin() || auth()->user()->isProductOwner())
                ? User::orderBy('name')->get()
                : collect(),
        ]);
    }
}
