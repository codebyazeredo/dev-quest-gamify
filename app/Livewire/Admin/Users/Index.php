<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use RequiresAdminAccess;
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
        $user = User::findOrFail($userId);

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
        $user = User::findOrFail($userId);

        $this->authorize('delete', $user);

        if ($user->xpTransactions()->exists()) {
            $this->addError('delete', 'Não é possível excluir um usuário que já possui histórico de atividade.');

            return;
        }

        $user->delete();
    }

    public function render(): View
    {
        return view('livewire.admin.users.index', [
            'users' => User::with('person')->orderBy('name')->paginate(15),
        ]);
    }
}
