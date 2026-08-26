<?php

namespace App\Livewire\Admin\Roles;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    public string $name = '';

    public function mount(): void
    {
        $this->authorize('accessAdminPanel', User::class);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'alpha_dash', 'unique:roles,name'],
        ];
    }

    public function save(): void
    {
        $this->authorize('accessAdminPanel', User::class);

        $validated = $this->validate();

        Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        $this->dispatch('role-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.roles.create');
    }
}
