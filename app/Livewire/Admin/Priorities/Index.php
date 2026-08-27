<?php

namespace App\Livewire\Admin\Priorities;

use App\Exceptions\DeletionBlockedException;
use App\Livewire\Concerns\FlushesToasts;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Livewire\Concerns\WithAdjustablePerPage;
use App\Models\TaskPriority;
use App\Repositories\TaskPriorityRepository;
use App\Services\Admin\PriorityService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use FlushesToasts;
    use RequiresAdminAccess;
    use WithAdjustablePerPage;
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
        $priority = app(TaskPriorityRepository::class)->findOrFail($priorityId);

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
        $priority = app(TaskPriorityRepository::class)->findOrFail($priorityId);

        $this->authorize('delete', $priority);

        $name = $priority->name;

        try {
            app(PriorityService::class)->delete($priority);
        } catch (DeletionBlockedException $e) {
            $this->addError('delete', $e->getMessage());
            $this->toastError('Não foi possível excluir', $e->getMessage());
            $this->flushToasts();

            return;
        }

        $this->toastSuccess('Prioridade excluída', "\"{$name}\" foi excluída.");
        $this->flushToasts();
    }

    public function render(): View
    {
        return view('livewire.admin.priorities.index', [
            'priorities' => app(TaskPriorityRepository::class)->paginate($this->perPage),
        ]);
    }
}
