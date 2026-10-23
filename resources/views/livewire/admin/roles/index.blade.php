<div>
    <x-page-header title="Roles & permissões" subtitle="Marque o que cada papel pode fazer." :back="route('admin.index')" backLabel="Configurações">
        <x-button wire:click="toggleCreate"><x-icon name="plus" /> Novo role</x-button>
    </x-page-header>

    @error('delete') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror

    <x-data-table :paginator="$roles">
        <x-slot:head>
            <th class="ps-4">Role</th>
            @foreach ($permissions as $permission)
                <th class="text-center">{{ \App\Support\PermissionLabel::for($permission->name) }}</th>
            @endforeach
            <th class="text-end pe-4"></th>
        </x-slot:head>

        @forelse ($roles as $role)
            <tr wire:key="role-{{ $role->id }}">
                <td class="ps-4 fw-medium">
                    {{ \App\Enums\UserRole::labelFor($role->name) }}
                    @if ($role->name === 'admin')
                        <x-badge color="warning" class="ms-1">acesso total</x-badge>
                    @endif
                </td>
                @foreach ($permissions as $permission)
                    <td class="text-center">
                        <div class="form-check d-flex justify-content-center mb-0">
                            <input type="checkbox" class="form-check-input"
                                aria-label="{{ \App\Support\PermissionLabel::for($permission->name) }}"
                                @checked($role->hasPermissionTo($permission))
                                @disabled($role->name === 'admin')
                                wire:click="togglePermission({{ $role->id }}, {{ $permission->id }})">
                        </div>
                    </td>
                @endforeach
                <td class="text-end pe-4">
                    @unless ($role->name === 'admin')
                        <x-row-actions :delete="'delete('.$role->id.')'" confirm="Excluir este role?" />
                    @endunless
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $permissions->count() + 2 }}" class="text-center text-body-secondary py-5">Nenhum role cadastrado.</td>
            </tr>
        @endforelse
    </x-data-table>

    @if ($showCreateModal)
        <livewire:admin.roles.create wire:key="role-create" />
    @endif
</div>
