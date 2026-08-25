<?php

namespace App\Livewire\Task;

use App\Enums\TaskPriority;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Services\TaskService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    public Task $task;

    public string $title;

    public string $description;

    public int $category_id;

    public int $priority;

    public ?string $due_at;

    public function mount(int $taskId): void
    {
        $this->task = Task::findOrFail($taskId);

        $this->authorize('update', $this->task);

        $this->title = $this->task->title;
        $this->description = (string) $this->task->description;
        $this->category_id = $this->task->category_id;
        $this->priority = $this->task->priority->value;
        $this->due_at = $this->task->due_at?->format('Y-m-d\TH:i');
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
            'due_at' => ['nullable', 'date'],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->task);

        $validated = $this->validate();

        app(TaskService::class)->updateDetails($this->task, $validated);

        $this->dispatch('task-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.task.edit', [
            'categories' => TaskCategory::orderBy('name')->get(),
            'priorities' => TaskPriority::cases(),
            'locked' => $this->task->completed_at !== null,
        ]);
    }
}
