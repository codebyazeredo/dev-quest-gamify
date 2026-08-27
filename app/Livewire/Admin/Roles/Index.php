<?php

namespace App\Livewire\Admin\Roles;

use App\Exceptions\DeletionBlockedException;
use App\Livewire\Concerns\FlushesToasts;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Livewire\Concerns\WithAdjustablePerPage;
use App\Models\User;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use App\Services\Admin\RoleService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use FlushesToasts;
    use RequiresAdminAccess;
    use WithAdjustablePerPage;
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

        $role = app(RoleRepository::class)->findOrFail($roleId);

        app(RoleService::class)->togglePermission($role, $permissionId);
    }

    public function delete(int $roleId): void
    {
        $this->authorize('accessAdminPanel', User::class);

        $role = app(RoleRepository::class)->findOrFail($roleId);
        $name = $role->name;

        try {
            app(RoleService::class)->delete($role);
        } catch (DeletionBlockedException $e) {
            $this->addError('delete', $e->getMessage());
            $this->toastError('Não foi possível excluir', $e->getMessage());
            $this->flushToasts();

            return;
        }

        $this->toastSuccess('Papel excluído', "\"{$name}\" foi excluído.");
        $this->flushToasts();
    }

    public function render(): View
    {
        return view('livewire.admin.roles.index', [
            'roles' => app(RoleRepository::class)->paginate($this->perPage),
            'permissions' => app(PermissionRepository::class)->all(),
        ]);
    }
}
