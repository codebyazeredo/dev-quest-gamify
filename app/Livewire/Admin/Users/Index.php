<?php

namespace App\Livewire\Admin\Users;

use App\Exceptions\DeletionBlockedException;
use App\Livewire\Concerns\FlushesToasts;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Livewire\Concerns\WithAdjustablePerPage;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Admin\UserService;
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

    public ?int $editingUserId = null;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function toggleCreate(): void
    {
        $this->authorize('create', User::class);

        $this->showCreateModal = ! $this->showCreateModal;
    }

    public function edit(int $userId): void
    {
        $user = app(UserRepository::class)->findOrFail($userId);

        $this->authorize('update', $user);

        $this->editingUserId = $userId;
    }

    #[On('close-modal')]
    #[On('user-saved')]
    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->editingUserId = null;
    }

    public function delete(int $userId): void
    {
        $user = app(UserRepository::class)->findOrFail($userId);

        $this->authorize('delete', $user);

        $name = $user->name;

        try {
            app(UserService::class)->delete($user);
        } catch (DeletionBlockedException $e) {
            $this->addError('delete', $e->getMessage());
            $this->toastError('Não foi possível excluir', $e->getMessage());
            $this->flushToasts();

            return;
        }

        $this->toastSuccess('Usuário excluído', "\"{$name}\" foi excluído.");
        $this->flushToasts();
    }

    public function render(): View
    {
        return view('livewire.admin.users.index', [
            'users' => app(UserRepository::class)->paginate($this->perPage),
        ]);
    }
}
