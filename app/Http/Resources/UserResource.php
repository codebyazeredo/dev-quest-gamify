<?php

namespace App\Http\Resources;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $roleNames = $this->getRoleNames();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'initials' => $this->initials(),
            'photo_url' => $this->photo_url,
            'roles' => $roleNames->values(),
            'role_labels' => $roleNames->map(fn (string $name) => UserRole::labelFor($name))->values(),
        ];
    }
}
