<?php

namespace App\Livewire\Admin\EventRules;

use App\Enums\TaskEventType;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\TaskEventRule;
use App\Repositories\TaskEventRuleRepository;
use App\Services\Admin\EventRuleService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    use FlushesToasts;

    public TaskEventRule $rule;

    public int $xp_reward = 0;

    public bool $active = true;

    public function mount(int $typeValue): void
    {
        $this->rule = app(TaskEventRuleRepository::class)->findByTypeOrFail(TaskEventType::from($typeValue));

        $this->authorize('update', $this->rule);

        $this->xp_reward = $this->rule->xp_reward;
        $this->active = $this->rule->active;
    }

    protected function rules(): array
    {
        return [
            'xp_reward' => array_filter([
                'required', 'integer', 'min:0', $this->rule->type->isPercentageBased() ? 'max:100' : null,
            ]),
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->rule);

        $validated = $this->validate();

        $rule = app(EventRuleService::class)->update($this->rule, $validated['xp_reward'], $this->active);

        $this->toastSuccess('Regra atualizada', "\"{$rule->type->label()}\" foi atualizada.");
        $this->flushToasts();

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
