<div>
    <x-page-header title="Usuários" subtitle="Contas de acesso ao sistema." :back="route('admin.index')" backLabel="Configurações">
        <a href="{{ route('admin.people') }}" class="btn btn-outline-secondary bg-body">
            <x-icon name="id-card" /> Gerenciar pessoas
        </a>

        @can('create', \App\Models\User::class)
            <x-button wire:click="toggleCreate"><x-icon name="plus" /> Novo usuário</x-button>
        @endcan
    </x-page-header>

    @error('delete') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror

    <x-data-table :paginator="$users">
        <x-slot:head>
            <th class="ps-4">Nome</th>
            <th>E-mail</th>
            <th>Roles</th>
            <th class="text-end pe-4"></th>
        </x-slot:head>

        @forelse ($users as $user)
            <tr wire:key="user-{{ $user->id }}">
                <td class="ps-4">
                    <div class="d-flex align-items-center gap-2">
                        <x-avatar :user="$user" size="sm" />
                        <span class="fw-medium">{{ $user->name }}</span>
                    </div>
                </td>
                <td>{{ $user->email }}</td>
                <td>
                    <div class="d-flex flex-wrap gap-1">
                        @forelse ($user->getRoleNames() as $roleName)
                            <x-badge color="primary">{{ \App\Enums\UserRole::labelFor($roleName) }}</x-badge>
                        @empty
                            <span class="small text-body-secondary">Sem role</span>
                        @endforelse
                    </div>
                </td>
                <td class="text-end pe-4">
                    <x-row-actions :edit="'edit('.$user->id.')'" :delete="'delete('.$user->id.')'" confirm="Excluir este usuário?" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-body-secondary py-5">Nenhum usuário cadastrado.</td>
            </tr>
        @endforelse
    </x-data-table>

    @if ($showCreateModal)
        <livewire:admin.users.create wire:key="user-create" />
    @endif

    @if ($editingUserId)
        <livewire:admin.users.edit :user-id="$editingUserId" wire:key="user-edit-{{ $editingUserId }}" />
    @endif
</div>
