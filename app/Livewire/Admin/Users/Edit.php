<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    public User $user;

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /** @var array<int, string> */
    public array $roles = [];

    public function mount(int $userId): void
    {
        $this->user = User::findOrFail($userId);

        $this->authorize('update', $this->user);

        $this->email = $this->user->email;
        $this->roles = $this->user->getRoleNames()->toArray();
    }

    /**
     * @return array<string, mixed>
     */
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

        $this->user->email = $validated['email'];

        if ($this->password !== '') {
            $this->user->password = $this->password;
        }

        $this->user->save();
        $this->user->syncRoles($validated['roles']);

        $this->dispatch('user-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.users.edit', [
            'availableRoles' => Role::orderBy('name')->pluck('name'),
        ]);
    }
}
