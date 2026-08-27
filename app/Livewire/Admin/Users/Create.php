<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\Person;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    use FlushesToasts;

    public ?int $personId = null;

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public array $roles = [];

    public function mount(): void
    {
        $this->authorize('create', User::class);
    }

    protected function rules(): array
    {
        return [
            'personId' => ['required', 'integer', Rule::exists('people', 'id')],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => [Rule::exists('roles', 'name')],
        ];
    }

    protected function validationAttributes(): array
    {
        return ['personId' => 'pessoa'];
    }

    public function save(): void
    {
        $this->authorize('create', User::class);

        $validated = $this->validate();

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

        $this->toastSuccess('Usuário criado', "\"{$person->nome}\" foi criado.");
        $this->flushToasts();

        $this->dispatch('user-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.users.create', [
            'availableRoles' => Role::orderBy('name')->pluck('name'),
            'availablePeople' => Person::whereDoesntHave('user')->orderBy('nome')->get(),
        ]);
    }
}
