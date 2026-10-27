<?php

namespace App\Support;

use App\Models\Address;

class AddressPayload
{
    /** @return array<string, mixed> */
    public static function of(?Address $address): array
    {
        return [
            'country_id' => $address?->country_id,
            'cep' => $address?->cep,
            'logradouro' => $address?->logradouro,
            'numero' => $address?->numero,
            'complemento' => $address?->complemento,
            'bairro' => $address?->bairro,
            'cidade' => $address?->cidade,
            'estado' => $address?->estado,
            'referencia' => $address?->referencia,
            'latitude' => $address?->latitude,
            'longitude' => $address?->longitude,
        ];
    }
}
