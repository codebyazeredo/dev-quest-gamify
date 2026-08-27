<?php

namespace App\Livewire\Admin\Users;

use App\Exceptions\DuplicateEntryException;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\User;
use App\Repositories\PersonRepository;
use App\Repositories\RoleRepository;
use App\Services\Admin\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

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

        try {
            $user = app(UserService::class)->create(
                $validated['personId'],
                $validated['email'],
                $validated['password'],
                $validated['roles'],
            );
        } catch (DuplicateEntryException $e) {
            $this->addError('personId', $e->getMessage());

            return;
        }

        $this->toastSuccess('Usuário criado', "\"{$user->name}\" foi criado.");
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
            'availableRoles' => app(RoleRepository::class)->names(),
            'availablePeople' => app(PersonRepository::class)->withoutUser(),
        ]);
    }
}
