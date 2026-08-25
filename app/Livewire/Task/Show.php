<?php

namespace App\Livewire\Task;

use App\Enums\TaskEventType;
use App\Enums\XpSourceType;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\Task;
use App\Models\User;
use App\Models\XpTransaction;
use App\Services\TaskService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    use FlushesToasts;

    public Task $task;

    public bool $showEditModal = false;

    public ?int $assignToUserId = null;

    public function mount(Task $task): void
    {
        $this->authorize('view', $task);

        $this->task = $task->load([
            'board', 'column', 'category', 'assignedTo', 'createdBy',
            'taskEvents.user', 'taskEvents.xpTransaction',
        ]);
    }

    public function toggleEdit(): void
    {
        $this->authorize('update', $this->task);

        $this->showEditModal = ! $this->showEditModal;
    }

    #[On('task-saved')]
    public function taskSaved(): void
    {
        $this->task->refresh();
        $this->showEditModal = false;
    }

    #[On('close-modal')]
    public function closeModal(): void
    {
        $this->showEditModal = false;
    }

    public function claim(): void
    {
        $this->authorize('claim', $this->task);

        app(TaskService::class)->assign($this->task, auth()->user());

        $this->task->refresh();
        $this->flushToasts();
    }

    public function assignTo(): void
    {
        $this->authorize('assignAny', Task::class);

        if (! $this->assignToUserId) {
            return;
        }

        app(TaskService::class)->assign($this->task, User::findOrFail($this->assignToUserId));

        $this->assignToUserId = null;
        $this->task->refresh();
        $this->flushToasts();
    }

    public function markHomologationCompleted(): void
    {
        $this->authorize('markHomologationCompleted', $this->task);

        app(TaskService::class)->markHomologationCompleted($this->task, auth()->user());

        $this->reloadTaskEvents();
        $this->flushToasts();
    }

    public function markDeployed(): void
    {
        $this->authorize('markDeployed', $this->task);

        app(TaskService::class)->markDeployed($this->task, auth()->user());

        $this->reloadTaskEvents();
        $this->flushToasts();
    }

    protected function reloadTaskEvents(): void
    {
        $this->task->load(['taskEvents.user', 'taskEvents.xpTransaction']);
    }

    public function render(): View
    {
        $completionBonus = XpTransaction::where('source_type', XpSourceType::TASK)
            ->where('source_id', $this->task->id)
            ->first();

        return view('livewire.task.show', [
            'developers' => auth()->user()->isAdmin() ? User::orderBy('name')->get() : collect(),
            'completionBonus' => $completionBonus,
            'hasHomologation' => $this->task->taskEvents->contains(fn ($e) => $e->type === TaskEventType::HOMOLOGATION_COMPLETED),
            'hasDeployed' => $this->task->taskEvents->contains(fn ($e) => $e->type === TaskEventType::DEPLOYED),
        ]);
    }
}
