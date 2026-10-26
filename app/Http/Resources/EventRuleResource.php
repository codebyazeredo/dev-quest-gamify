<?php

namespace App\Http\Resources;

use App\Enums\TaskEventType;
use App\Models\TaskEventRule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Linha da tela de regras de XP: um tipo de evento e, se já configurada, a sua regra.
 *
 * @property array{type: TaskEventType, rule: ?TaskEventRule} $resource
 */
class EventRuleResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $type = $this->resource['type'];
        $rule = $this->resource['rule'];

        return [
            'type' => $type->value,
            'label' => $type->label(),
            'is_percentage' => $type->isPercentageBased(),
            'configured' => $rule !== null,
            'xp_reward' => $rule?->xp_reward,
            'active' => $rule?->active,
        ];
    }
}
