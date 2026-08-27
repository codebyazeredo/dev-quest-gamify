<?php

namespace App\Services\Admin;

use App\Exceptions\DeletionBlockedException;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use Spatie\Permission\Models\Role;

class RoleService
{
    private const PROTECTED_ROLE = 'admin';

    public function __construct(
        private RoleRepository $roles,
        private PermissionRepository $permissions,
    ) {}

    public function create(string $name): Role
    {
        return $this->roles->create(['name' => $name, 'guard_name' => 'web']);
    }

    public function togglePermission(Role $role, int $permissionId): void
    {
        if ($role->name === self::PROTECTED_ROLE) {
            return;
        }

        $permission = $this->permissions->findOrFail($permissionId);

        if ($this->roles->hasPermission($role, $permission)) {
            $this->roles->revokePermission($role, $permission);
        } else {
            $this->roles->givePermission($role, $permission);
        }
    }

    public function delete(Role $role): void
    {
        if ($role->name === self::PROTECTED_ROLE) {
            throw new DeletionBlockedException('O role "admin" não pode ser excluído.');
        }

        if ($this->roles->hasUsers($role)) {
            throw new DeletionBlockedException('Este role está em uso por algum usuário.');
        }

        $this->roles->delete($role);
    }
}
