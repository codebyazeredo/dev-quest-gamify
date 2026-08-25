<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Users extends Component
{
    use RequiresAdminAccess;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public int $role = UserRole::DEVELOPER->value;

    public ?int $editingId = null;

    public string $editingName = '';

    public string $editingEmail = '';

    public string $editingPassword = '';

    public string $editingPasswordConfirmation = '';

    public int $editingRole = UserRole::DEVELOPER->value;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    /**
     * @return array<int>
     */
    protected function assignableRoles(): array
    {
        return [UserRole::PRODUCT_OWNER->value, UserRole::DEVELOPER->value];
    }

    public function create(): void
    {
        $this->authorize('create', User::class);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in($this->assignableRoles())],
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => UserRole::from($this->role),
        ]);

        $this->reset('name', 'email', 'password', 'password_confirmation', 'role');
        $this->role = UserRole::DEVELOPER->value;
    }

    public function edit(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->authorize('update', $user);

        $this->editingId = $user->id;
        $this->editingName = $user->name;
        $this->editingEmail = $user->email;
        $this->editingPassword = '';
        $this->editingPasswordConfirmation = '';
        $this->editingRole = $user->role->value;
    }

    public function update(): void
    {
        $user = User::findOrFail($this->editingId);

        $this->authorize('update', $user);

        $this->validate([
            'editingName' => ['required', 'string', 'max:255'],
            'editingEmail' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'editingPassword' => ['nullable', 'confirmed', Password::defaults()],
            'editingRole' => ['required', Rule::in($this->assignableRoles())],
        ], [], ['editingPassword' => 'password']);

        $user->name = $this->editingName;
        $user->email = $this->editingEmail;
        $user->role = UserRole::from($this->editingRole);

        if ($this->editingPassword !== '') {
            $user->password = $this->editingPassword;
        }

        $user->save();

        $this->editingId = null;
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
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
        return view('livewire.admin.users', [
            'users' => User::whereIn('role', $this->assignableRoles())->orderBy('name')->get(),
            'roles' => UserRole::cases(),
            'assignableRoles' => $this->assignableRoles(),
        ]);
    }
}
