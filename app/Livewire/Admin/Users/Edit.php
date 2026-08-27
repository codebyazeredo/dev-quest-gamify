<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\Admin\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Edit extends Component
{
    use FlushesToasts;

    public User $user;

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public array $roles = [];

    public function mount(int $userId): void
    {
        $this->user = app(UserRepository::class)->findOrFail($userId);

        $this->authorize('update', $this->user);

        $this->email = $this->user->email;
        $this->roles = $this->user->getRoleNames()->toArray();
    }

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => [Rule::exists('roles', 'name')],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->user);

        $validated = $this->validate();

        $user = app(UserService::class)->update(
            $this->user,
            $validated['email'],
            $this->password !== '' ? $this->password : null,
            $validated['roles'],
        );

        $this->toastSuccess('Usuário atualizado', "\"{$user->name}\" foi atualizado.");
        $this->flushToasts();

        $this->dispatch('user-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.users.edit', [
            'availableRoles' => app(RoleRepository::class)->names(),
        ]);
    }
}
