<?php

namespace App\Livewire\Admin\EventRules;

use App\Enums\TaskEventType;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\TaskEventRule;
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
        $configured = TaskEventRule::pluck('type')->map(fn (TaskEventType $type) => $type->value)->all();

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

        if (TaskEventRule::where('type', $type)->exists()) {
            $this->addError('type', 'Este evento já possui uma regra configurada.');

            return;
        }

        TaskEventRule::create([
            'type' => $type,
            'xp_reward' => $validated['xp_reward'],
            'active' => $this->active,
        ]);

        $this->toastSuccess('Regra criada', "\"{$type->label()}\" foi criada.");
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
