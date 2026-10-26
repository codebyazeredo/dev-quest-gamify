<?php

namespace App\Http\Resources;

use App\Models\BoardColumn;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin BoardColumn */
class BoardColumnResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'status' => $this->status?->value,
            'is_final' => $this->is_final,
        ];
    }
}
