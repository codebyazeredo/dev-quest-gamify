<?php

namespace App\Http\Resources;

use App\Models\Person;
use App\Support\AddressPayload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Person */
class PersonResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'rg' => $this->rg,
            'nascimento' => $this->nascimento->format('Y-m-d'),
            'sexo' => $this->sexo->value,
            'email' => $this->email,
            'telefone1' => $this->telefone1,
            'telefone2' => $this->telefone2,
            'foto_url' => $this->foto_url,
            'has_user' => (bool) $this->user_exists,
            ...AddressPayload::of($this->address),
        ];
    }
}
