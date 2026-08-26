<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\Person;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
class Users extends Component
{
    use RequiresAdminAccess;

    public ?int $personId = null;

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /** @var array<int, string> */
    public array $roles = [];

    public ?int $editingId = null;

    public string $editingEmail = '';

    public string $editingPassword = '';

    public string $editingPasswordConfirmation = '';

    /** @var array<int, string> */
    public array $editingRoles = [];

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function create(): void
    {
        $this->authorize('create', User::class);

        $validated = $this->validate([
            'personId' => [
                'required',
                'integer',
                Rule::exists('people', 'id'),
            ],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => [Rule::exists('roles', 'name')],
        ], [], ['personId' => 'pessoa']);

        $person = Person::findOrFail($validated['personId']);

        if ($person->user()->exists()) {
            $this->addError('personId', 'Esta pessoa já possui um usuário.');

            return;
        }

        $user = User::create([
            'name' => $person->nome,
            'email' => $validated['email'],
            'password' => $validated['password'],
            'person_id' => $person->id,
        ]);

        $user->syncRoles($validated['roles']);

        $this->reset('personId', 'email', 'password', 'password_confirmation', 'roles');
    }

    public function edit(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->authorize('update', $user);

        $this->editingId = $user->id;
        $this->editingEmail = $user->email;
        $this->editingPassword = '';
        $this->editingPasswordConfirmation = '';
        $this->editingRoles = $user->getRoleNames()->toArray();
    }

    public function update(): void
    {
        $user = User::findOrFail($this->editingId);

        $this->authorize('update', $user);

        $validated = $this->validate([
            'editingEmail' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'editingPassword' => ['nullable', 'confirmed', Password::defaults()],
            'editingRoles' => ['required', 'array', 'min:1'],
            'editingRoles.*' => [Rule::exists('roles', 'name')],
        ], [], ['editingPassword' => 'password']);

        $user->email = $validated['editingEmail'];

        if ($this->editingPassword !== '') {
            $user->password = $this->editingPassword;
        }

        $user->save();
        $user->syncRoles($validated['editingRoles']);

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
            'users' => User::with('person')->orderBy('name')->get(),
            'availableRoles' => Role::orderBy('name')->pluck('name'),
            'availablePeople' => Person::whereDoesntHave('user')->orderBy('nome')->get(),
        ]);
    }
}
