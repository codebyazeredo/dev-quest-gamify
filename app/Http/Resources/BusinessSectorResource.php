<?php

namespace App\Http\Resources;

use App\Models\BusinessSector;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin BusinessSector */
class BusinessSectorResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'clients_count' => $this->clients_count ?? 0,
        ];
    }
}
