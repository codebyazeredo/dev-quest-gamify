<?php

namespace App\Livewire\Admin;

use App\Enums\TaskPriority;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\TaskPriorityRule;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class PriorityRules extends Component
{
    use RequiresAdminAccess;

    public ?int $editingPriority = null;

    public string $editingMultiplier = '0';

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function edit(int $priorityValue): void
    {
        $priority = TaskPriority::from($priorityValue);

        $this->authorize('update', $this->ruleFor($priority));

        $this->editingPriority = $priority->value;
        $this->editingMultiplier = (string) TaskPriorityRule::multiplierFor($priority);
    }

    public function update(): void
    {
        $priority = TaskPriority::from($this->editingPriority);
        $rule = $this->ruleFor($priority);

        $this->authorize('update', $rule);

        $this->validate([
            'editingMultiplier' => ['required', 'numeric', 'min:0.01', 'max:99.99'],
        ]);

        $rule->exists
            ? $rule->update(['multiplier' => $this->editingMultiplier])
            : TaskPriorityRule::create(['priority' => $priority, 'multiplier' => $this->editingMultiplier]);

        $this->editingPriority = null;
    }

    public function cancelEdit(): void
    {
        $this->editingPriority = null;
    }

    protected function ruleFor(TaskPriority $priority): TaskPriorityRule
    {
        return TaskPriorityRule::where('priority', $priority)->first()
            ?? new TaskPriorityRule(['priority' => $priority]);
    }

    public function render(): View
    {
        $rows = collect(TaskPriority::cases())->map(fn (TaskPriority $priority) => [
            'priority' => $priority,
            'multiplier' => TaskPriorityRule::multiplierFor($priority),
        ]);

        return view('livewire.admin.priority-rules', ['rows' => $rows]);
    }
}
