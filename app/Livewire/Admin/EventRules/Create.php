<?php

namespace App\Livewire\Admin\EventRules;

use App\Enums\TaskEventType;
use App\Exceptions\DuplicateEntryException;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\TaskEventRule;
use App\Repositories\TaskEventRuleRepository;
use App\Services\Admin\EventRuleService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    use FlushesToasts;

    public ?int $type = null;

    public int $xp_reward = 0;

    public bool $active = true;

    public function mount(): void
    {
        $this->authorize('create', TaskEventRule::class);
    }

    protected function availableTypes(): array
    {
        $configured = app(TaskEventRuleRepository::class)->configuredTypes()
            ->map(fn (TaskEventType $type) => $type->value)
            ->all();

        return array_values(array_filter(TaskEventType::cases(), fn (TaskEventType $type) => ! in_array($type->value, $configured, true)));
    }

    protected function rules(): array
    {
        $isPercentage = $this->type !== null && TaskEventType::from($this->type)->isPercentageBased();

        return [
            'type' => ['required', 'integer'],
            'xp_reward' => array_filter([
                'required', 'integer', 'min:0', $isPercentage ? 'max:100' : null,
            ]),
        ];
    }

    public function save(): void
    {
        $this->authorize('create', TaskEventRule::class);

        $validated = $this->validate();

        $type = TaskEventType::from($validated['type']);

        try {
            $rule = app(EventRuleService::class)->create($type, $validated['xp_reward'], $this->active);
        } catch (DuplicateEntryException $e) {
            $this->addError('type', $e->getMessage());

            return;
        }

        $this->toastSuccess('Regra criada', "\"{$rule->type->label()}\" foi criada.");
        $this->flushToasts();

        $this->dispatch('event-rule-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.event-rules.create', [
            'availableTypes' => $this->availableTypes(),
        ]);
    }
}
