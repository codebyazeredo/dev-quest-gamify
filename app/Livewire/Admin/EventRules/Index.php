<?php

namespace App\Livewire\Admin\EventRules;

use App\Enums\TaskEventType;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\TaskEventRule;
use App\Repositories\TaskEventRuleRepository;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use RequiresAdminAccess;

    public bool $showCreateModal = false;

    public ?int $editingType = null;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function toggleCreate(): void
    {
        $this->authorize('create', TaskEventRule::class);

        $this->showCreateModal = ! $this->showCreateModal;
    }

    public function edit(int $typeValue): void
    {
        $rule = app(TaskEventRuleRepository::class)->findByTypeOrFail(TaskEventType::from($typeValue));

        $this->authorize('update', $rule);

        $this->editingType = $typeValue;
    }

    #[On('close-modal')]
    #[On('event-rule-saved')]
    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->editingType = null;
    }

    public function render(): View
    {
        $repository = app(TaskEventRuleRepository::class);
        $configuredTypes = $repository->configuredTypes();

        $rows = collect(TaskEventType::cases())->map(fn (TaskEventType $type) => [
            'type' => $type,
            'rule' => $repository->findByType($type),
        ]);

        return view('livewire.admin.event-rules.index', [
            'rows' => $rows,
            'hasUnconfiguredTypes' => $configuredTypes->count() < count(TaskEventType::cases()),
        ]);
    }
}
