<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
class Roles extends Component
{
    use RequiresAdminAccess;

    public string $name = '';

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function create(): void
    {
        $this->authorize('accessAdminPanel', User::class);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:60', 'alpha_dash', 'unique:roles,name'],
        ]);

        Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        $this->reset('name');
    }

    public function togglePermission(int $roleId, int $permissionId): void
    {
        $this->authorize('accessAdminPanel', User::class);

        $role = Role::findOrFail($roleId);

        if ($role->name === 'admin') {
            return;
        }

        $permission = Permission::findOrFail($permissionId);

        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
        } else {
            $role->givePermissionTo($permission);
        }
    }

    public function delete(int $roleId): void
    {
        $this->authorize('accessAdminPanel', User::class);

        $role = Role::findOrFail($roleId);

        if ($role->name === 'admin') {
            $this->addError('delete', 'O role "admin" não pode ser excluído.');

            return;
        }

        if ($role->users()->exists()) {
            $this->addError('delete', 'Não é possível excluir um role em uso por algum usuário.');

            return;
        }

        $role->delete();
    }

    public function render(): View
    {
        return view('livewire.admin.roles', [
            'roles' => Role::with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }
}
