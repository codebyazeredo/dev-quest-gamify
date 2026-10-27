<?php

namespace App\Http\Requests\Admin;

use App\Enums\TaskEventType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRuleRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $isPercentage = $this->eventType()?->isPercentageBased() ?? false;

        return [
            'type' => $this->isMethod('POST') ? ['required', 'integer', Rule::enum(TaskEventType::class)] : [],
            'xp_reward' => array_filter(['required', 'integer', 'min:0', $isPercentage ? 'max:100' : null]),
            'active' => ['required', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['type' => 'evento', 'xp_reward' => 'recompensa de XP', 'active' => 'status'];
    }

    private function eventType(): ?TaskEventType
    {
        $typeValue = $this->route('type') ?? $this->input('type');

        return is_numeric($typeValue) ? TaskEventType::tryFrom((int) $typeValue) : null;
    }
}
