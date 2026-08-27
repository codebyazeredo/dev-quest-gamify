<?php

namespace App\Livewire\Admin\Roles;

use App\Livewire\Concerns\FlushesToasts;
use App\Models\User;
use App\Services\Admin\RoleService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    use FlushesToasts;

    public string $name = '';

    public function mount(): void
    {
        $this->authorize('accessAdminPanel', User::class);
    }

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

        $role = app(RoleService::class)->create($validated['name']);

        $this->toastSuccess('Papel criado', "\"{$role->name}\" foi criado.");
        $this->flushToasts();

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
