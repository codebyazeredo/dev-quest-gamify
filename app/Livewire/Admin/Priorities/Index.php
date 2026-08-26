<?php

namespace App\Livewire\Admin\Priorities;

use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\TaskPriority;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use RequiresAdminAccess;
    use WithPagination;

    public bool $showCreateModal = false;

    public ?int $editingPriorityId = null;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function toggleCreate(): void
    {
        $this->authorize('create', TaskPriority::class);

        $this->showCreateModal = ! $this->showCreateModal;
    }

    public function edit(int $priorityId): void
    {
        $priority = TaskPriority::findOrFail($priorityId);

        $this->authorize('update', $priority);

        $this->editingPriorityId = $priorityId;
    }

    #[On('close-modal')]
    #[On('priority-saved')]
    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->editingPriorityId = null;
    }

    public function delete(int $priorityId): void
    {
        $priority = TaskPriority::findOrFail($priorityId);

        $this->authorize('delete', $priority);

        if ($priority->tasks()->exists()) {
            $this->addError('delete', 'Não é possível excluir uma gravidade que ainda possui tarefas.');

            return;
        }

        $priority->delete();
    }

    public function render(): View
    {
        return view('livewire.admin.priorities.index', [
            'priorities' => TaskPriority::orderBy('multiplier')->paginate(15),
        ]);
    }
}
