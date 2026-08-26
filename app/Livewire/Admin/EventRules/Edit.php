<?php

namespace App\Livewire\Admin\EventRules;

use App\Enums\TaskEventType;
use App\Models\TaskEventRule;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    public TaskEventRule $rule;

    public int $xp_reward = 0;

    public bool $active = true;

    public function mount(int $typeValue): void
    {
        $this->rule = TaskEventRule::where('type', TaskEventType::from($typeValue))->firstOrFail();

        $this->authorize('update', $this->rule);

        $this->xp_reward = $this->rule->xp_reward;
        $this->active = $this->rule->active;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'xp_reward' => ['required', 'integer', 'min:0'],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->rule);

        $validated = $this->validate();

        $this->rule->update([
            'xp_reward' => $validated['xp_reward'],
            'active' => $this->active,
        ]);

        $this->dispatch('event-rule-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.event-rules.edit');
    }
}
