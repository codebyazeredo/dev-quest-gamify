<?php

namespace App\Livewire\Admin;

use App\Models\TaskEventRule;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class EventRules extends Component
{
    public ?int $editingId = null;

    public int $editingXpReward = 0;

    public bool $editingActive = true;

    public function mount(): void
    {
        Gate::authorize('accessAdminPanel', User::class);
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
