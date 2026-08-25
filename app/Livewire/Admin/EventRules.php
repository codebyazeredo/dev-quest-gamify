<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\TaskEventRule;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class EventRules extends Component
{
    use RequiresAdminAccess;

    public ?int $editingId = null;

    public int $editingXpReward = 0;

    public bool $editingActive = true;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function edit(int $ruleId): void
    {
        $rule = TaskEventRule::findOrFail($ruleId);

        $this->authorize('update', $rule);

        $this->editingId = $rule->id;
        $this->editingXpReward = $rule->xp_reward;
        $this->editingActive = $rule->active;
    }

    public function update(): void
    {
        $rule = TaskEventRule::findOrFail($this->editingId);

        $this->authorize('update', $rule);

        $this->validate([
            'editingXpReward' => ['required', 'integer', 'min:0'],
        ]);

        $rule->update([
            'xp_reward' => $this->editingXpReward,
            'active' => $this->editingActive,
        ]);

        $this->editingId = null;
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
    }

    public function render(): View
    {
        return view('livewire.admin.event-rules', [
            'rules' => TaskEventRule::orderBy('type')->get(),
        ]);
    }
}
