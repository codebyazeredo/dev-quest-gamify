<?php

namespace App\Livewire\Task;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskPriority;
use App\Services\TaskService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    use FlushesToasts;

    public Task $task;

    public string $title;

    public string $description;

    public int $category_id;

    public int $priority_id;

    public ?string $due_at;

    public function mount(int $taskId): void
    {
        $this->task = Task::findOrFail($taskId);

        $this->authorize('update', $this->task);

        $this->title = $this->task->title;
        $this->description = (string) $this->task->description;
        $this->category_id = $this->task->category_id;
        $this->priority_id = $this->task->priority_id;
        $this->due_at = $this->task->due_at?->format('Y-m-d\TH:i');
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:task_categories,id'],
            'priority_id' => ['required', 'exists:task_priorities,id'],
            'due_at' => ['nullable', 'date'],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->task);

        $validated = $this->validate();

        app(TaskService::class)->updateDetails($this->task, $validated);

        $this->toastSuccess('Tarefa atualizada', "\"{$validated['title']}\" foi atualizada.");
        $this->flushToasts();

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
            'priorities' => TaskPriority::orderBy('multiplier')->get(),
            'locked' => $this->task->completed_at !== null,
        ]);
    }
}
