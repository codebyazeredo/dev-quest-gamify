<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Usuários</h1>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.people') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                Gerenciar pessoas
            </a>

            @can('create', \App\Models\User::class)
                <button type="button" wire:click="toggleCreate" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    + Novo usuário
                </button>
            @endcan
        </div>
    </div>

    @error('delete') <p class="mb-4 text-sm text-red-600">{{ $message }}</p> @enderror

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">E-mail</th>
                    <th class="px-4 py-2">Roles</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->name }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($user->getRoleNames() as $roleName)
                                    <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                        {{ \App\Enums\UserRole::labelFor($roleName) }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400">Sem role</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <button type="button" wire:click="edit({{ $user->id }})" title="Editar" aria-label="Editar"
                                class="rounded-md border border-gray-300 p-1.5 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </button>
                            <button type="button" wire:click="delete({{ $user->id }})" wire:confirm="Excluir este usuário?" title="Excluir" aria-label="Excluir"
                                class="ml-2 rounded-md border border-red-300 p-1.5 text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/30">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Nenhum usuário cadastrado.</td>
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
