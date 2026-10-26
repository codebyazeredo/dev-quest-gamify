<?php

namespace App\Http\Resources;

use App\Models\TaskCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskCategoryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'base_points' => $this->base_points,
            'color' => $this->color,
            'text_color' => $this->text_color,
        ];
    }
}
