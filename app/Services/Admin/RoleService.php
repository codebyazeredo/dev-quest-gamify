<?php

namespace App\Services\Admin;

use App\Exceptions\DeletionBlockedException;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class RoleService
{
    private const PROTECTED_ROLE = 'admin';

    public function __construct(
        private RoleRepository $roles,
        private PermissionRepository $permissions,
    ) {}

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->roles->paginate($perPage);
    }

    public function permissions(): Collection
    {
        return $this->permissions->all();
    }

    public function create(string $name): Role
    {
        return $this->roles->create(['name' => $name, 'guard_name' => 'web'])->load('permissions');
    }

    /** O role admin tem acesso total e não pode ter permissões alteradas. */
    public function togglePermission(Role $role, int $permissionId): Role
    {
        if ($role->name === self::PROTECTED_ROLE) {
            throw new DeletionBlockedException('As permissões do role "admin" não podem ser alteradas.');
        }

        $permission = $this->permissions->findOrFail($permissionId);

        if ($this->roles->hasPermission($role, $permission)) {
            $this->roles->revokePermission($role, $permission);
        } else {
            $this->roles->givePermission($role, $permission);
        }

        return $role->load('permissions');
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
