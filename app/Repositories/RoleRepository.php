<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleRepository extends Repository
{
    protected function model(): string
    {
        return Role::class;
    }

    protected function query(): Builder
    {
        return parent::query()->with('permissions')->orderBy('name');
    }

    public function names(): Collection
    {
        return Role::query()->orderBy('name')->pluck('name');
    }

    public function hasUsers(Role $role): bool
    {
        return $role->users()->exists();
    }

    public function hasPermission(Role $role, Permission $permission): bool
    {
        return $role->hasPermissionTo($permission);
    }

    public function givePermission(Role $role, Permission $permission): void
    {
        $role->givePermissionTo($permission);
    }

    public function revokePermission(Role $role, Permission $permission): void
    {
        $role->revokePermissionTo($permission);
    }
}
