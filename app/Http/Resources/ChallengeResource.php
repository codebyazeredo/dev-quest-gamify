<?php

namespace App\Http\Resources;

use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Challenge */
class ChallengeResource extends JsonResource
{
    private const DATETIME_INPUT_FORMAT = 'Y-m-d\TH:i';

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon ?? 'flag',
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'target' => $this->target,
            'xp_reward' => $this->xp_reward,
            'starts_at' => $this->starts_at->format(self::DATETIME_INPUT_FORMAT),
            'ends_at' => $this->ends_at->format(self::DATETIME_INPUT_FORMAT),
            'period_label' => $this->starts_at->format('d/m').' – '.$this->ends_at->format('d/m'),
            'active' => $this->active,
            'status_label' => $this->statusLabel(),
            'status_color' => $this->statusColor(),
            'participants_count' => $this->participants_count ?? 0,
            'completions_count' => $this->completions_count ?? 0,
            'titles' => $this->titles->map(fn ($title) => ['name' => $title->name, 'icon' => $title->icon ?? 'medal'])->values(),
        ];
    }
}
