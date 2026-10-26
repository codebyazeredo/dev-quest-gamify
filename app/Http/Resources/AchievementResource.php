<?php

namespace App\Http\Resources;

use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Achievement */
class AchievementResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon ?? 'trophy',
            'condition_type' => $this->condition_type->value,
            'condition_label' => $this->condition_type->label(),
            'condition_value' => $this->condition_value,
            'xp_reward' => $this->xp_reward,
            'active' => $this->active,
            'unlocks_count' => $this->user_achievements_count ?? 0,
            'titles' => $this->title ? [['name' => $this->title->name, 'icon' => $this->title->icon ?? 'medal']] : [],
        ];
    }
}
