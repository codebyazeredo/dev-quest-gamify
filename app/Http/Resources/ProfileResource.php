<?php

namespace App\Http\Resources;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Dados do próprio usuário para o formulário "Editar meus dados".
 *
 * @mixin Person
 */
class ProfileResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            ...PersonResource::make($this->resource)->toArray($request),
            'has_address' => $this->address !== null,
            'login_email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
        ];
    }
}
