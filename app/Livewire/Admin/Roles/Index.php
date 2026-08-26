<?php

namespace App\Livewire\Admin\Roles;

use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use RequiresAdminAccess;
    use WithPagination;

    public bool $showCreateModal = false;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function toggleCreate(): void
    {
        $this->authorize('accessAdminPanel', User::class);

        $this->showCreateModal = ! $this->showCreateModal;
    }

    #[On('close-modal')]
    #[On('role-saved')]
    public function closeModal(): void
    {
        $this->showCreateModal = false;
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
        return view('livewire.admin.roles.index', [
            'roles' => Role::with('permissions')->orderBy('name')->paginate(15),
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }
}
