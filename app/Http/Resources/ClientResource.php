<?php

namespace App\Http\Resources;

use App\Models\Client;
use App\Support\AddressPayload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Client */
class ClientResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'razao_social' => $this->razao_social,
            'cnpj' => $this->cnpj,
            'email' => $this->email,
            'business_sector_id' => $this->business_sector_id,
            'business_sector' => $this->businessSector?->name,
            'is_active' => $this->is_active,
            ...AddressPayload::of($this->address),
        ];
    }
}
