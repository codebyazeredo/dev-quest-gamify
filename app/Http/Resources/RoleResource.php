<?php

namespace App\Http\Resources;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Role;

/** @mixin Role */
class RoleResource extends JsonResource
{
    private const PROTECTED_ROLE = 'admin';

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => UserRole::labelFor($this->name),
            'is_protected' => $this->name === self::PROTECTED_ROLE,
            'permission_ids' => $this->permissions->pluck('id')->values(),
        ];
    }
}
