<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Usuários</h1>

        <a href="{{ route('admin.people') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
            Gerenciar pessoas
        </a>
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
                @foreach ($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        @if ($editingId === $user->id)
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->name }}</td>
                            <td class="px-4 py-2">
                                <input type="email" wire:model="editingEmail" class="w-full rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                @error('editingEmail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($availableRoles as $roleOption)
                                        <label class="flex items-center gap-1 rounded-md border border-gray-300 px-2 py-1 text-xs dark:border-gray-600">
                                            <input type="checkbox" value="{{ $roleOption }}" wire:model="editingRoles" class="rounded border-gray-300">
                                            {{ \App\Enums\UserRole::labelFor($roleOption) }}
                                        </label>
                                    @endforeach
                                </div>
                                @error('editingRoles') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                <div class="mt-2 flex gap-1">
                                    <input type="password" wire:model="editingPassword" placeholder="Nova senha (opcional)" class="w-full rounded-md border border-gray-300 px-2 py-1 text-xs dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                    <input type="password" wire:model="editingPasswordConfirmation" placeholder="Confirmar" class="w-full rounded-md border border-gray-300 px-2 py-1 text-xs dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                </div>
                                @error('editingPassword') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2 text-right align-top">
                                <button type="button" wire:click="update"
                                    class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-500">
                                    Salvar
                                </button>
                                <button type="button" wire:click="cancelEdit"
                                    class="ml-2 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                    Cancelar
                                </button>
                            </td>
                        @else
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
                                <button type="button" wire:click="edit({{ $user->id }})"
                                    class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                    Editar
                                </button>
                                <button type="button" wire:click="delete({{ $user->id }})" wire:confirm="Excluir este usuário?"
                                    class="ml-2 rounded-md border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/30">
                                    Excluir
                                </button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <form wire:submit="create" class="mt-6 space-y-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Novo usuário</h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pessoa</label>
            <select wire:model="personId" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                <option value="">Selecione uma pessoa cadastrada</option>
                @forelse ($availablePeople as $person)
                    <option value="{{ $person->id }}">{{ $person->nome }} ({{ $person->cpf }})</option>
                @empty
                    <option value="" disabled>Nenhuma pessoa disponível — cadastre em "Gerenciar pessoas"</option>
                @endforelse
            </select>
            @error('personId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Senha</label>
                <input type="password" wire:model="password" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
                <input type="email" wire:model="email" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar senha</label>
            <input type="password" wire:model="password_confirmation" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</label>
            <div class="mt-1 flex flex-wrap gap-2">
                @foreach ($availableRoles as $roleOption)
                    <label class="flex items-center gap-1 rounded-md border border-gray-300 px-2 py-1 text-xs dark:border-gray-600">
                        <input type="checkbox" value="{{ $roleOption }}" wire:model="roles" class="rounded border-gray-300">
                        {{ \App\Enums\UserRole::labelFor($roleOption) }}
                    </label>
                @endforeach
            </div>
            @error('roles') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            Adicionar usuário
        </button>
    </form>
</div>
