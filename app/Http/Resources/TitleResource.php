<?php

namespace App\Http\Resources;

use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Title */
class TitleResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon ?? 'medal',
            'unlock_type' => $this->unlock_type->value,
            'unlock_rule' => $this->unlockRuleDescription(),
            'achievement_id' => $this->achievement_id,
            'challenge_id' => $this->challenge_id,
            'challenges_required' => $this->challenges_required,
            'active' => $this->active,
            'unlocks_count' => $this->user_titles_count ?? 0,
        ];
    }
}
