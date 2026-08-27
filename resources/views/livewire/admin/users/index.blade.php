<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-ink">Usuários</h1>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.people') }}" class="rounded-lg border border-line px-3 py-1.5 text-sm font-medium text-ink hover:bg-line/20">
                Gerenciar pessoas
            </a>

            @can('create', \App\Models\User::class)
                <button type="button" wire:click="toggleCreate" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover">
                    + Novo usuário
                </button>
            @endcan
        </div>
    </div>

    @error('delete') <p class="mb-4 text-sm text-terracotta">{{ $message }}</p> @enderror

    <div class="overflow-x-auto rounded-xl border border-line bg-card">
        <table class="w-full text-sm">
            <thead class="bg-line/20 text-left text-ink-muted">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">E-mail</th>
                    <th class="px-4 py-3">Roles</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line/50">
                @forelse ($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td class="px-4 py-3 text-ink">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-ink">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($user->getRoleNames() as $roleName)
                                    <x-badge color="primary">{{ \App\Enums\UserRole::labelFor($roleName) }}</x-badge>
                                @empty
                                    <span class="text-xs text-ink-muted">Sem role</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="edit({{ $user->id }})" title="Editar" aria-label="Editar"
                                class="rounded-lg border border-line p-1.5 text-ink-muted hover:bg-line/20">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </button>
                            <button type="button" wire:click="delete({{ $user->id }})" wire:confirm="Excluir este usuário?" title="Excluir" aria-label="Excluir"
                                class="ml-2 rounded-lg border border-terracotta/30 p-1.5 text-terracotta hover:bg-terracotta/10">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-ink-muted">Nenhum usuário cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    @if ($showCreateModal)
        <livewire:admin.users.create wire:key="user-create" />
    @endif

    @if ($editingUserId)
        <livewire:admin.users.edit :user-id="$editingUserId" wire:key="user-edit-{{ $editingUserId }}" />
    @endif
</div>
