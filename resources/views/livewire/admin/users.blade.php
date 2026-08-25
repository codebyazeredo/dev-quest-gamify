<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Usuários</h1>

    @error('delete') <p class="mb-4 text-sm text-red-600">{{ $message }}</p> @enderror

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">E-mail</th>
                    <th class="px-4 py-2">Função</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        @if ($editingId === $user->id)
                            <td class="px-4 py-2">
                                <input type="text" wire:model="editingName" class="w-full rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                @error('editingName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2">
                                <input type="email" wire:model="editingEmail" class="w-full rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                @error('editingEmail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2">
                                <select wire:model="editingRole" class="rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                    @foreach ($roles as $roleOption)
                                        @if (in_array($roleOption->value, $assignableRoles, true))
                                            <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="mt-2 flex gap-1">
                                    <input type="password" wire:model="editingPassword" placeholder="Nova senha (opcional)" class="w-full rounded-md border border-gray-300 px-2 py-1 text-xs dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                    <input type="password" wire:model="editingPasswordConfirmation" placeholder="Confirmar" class="w-full rounded-md border border-gray-300 px-2 py-1 text-xs dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                </div>
                                @error('editingPassword') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" wire:click="update" class="text-indigo-600 hover:underline">Salvar</button>
                                <button type="button" wire:click="cancelEdit" class="ml-2 text-gray-500 hover:underline">Cancelar</button>
                            </td>
                        @else
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->name }}</td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->email }}</td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->role->label() }}</td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" wire:click="edit({{ $user->id }})" class="text-indigo-600 hover:underline">Editar</button>
                                <button type="button" wire:click="delete({{ $user->id }})" wire:confirm="Excluir este usuário?" class="ml-2 text-red-600 hover:underline">Excluir</button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <form wire:submit="create" class="mt-6 space-y-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
                <input type="email" wire:model="email" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Senha</label>
                <input type="password" wire:model="password" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar senha</label>
                <input type="password" wire:model="password_confirmation" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Função</label>
                <select wire:model="role" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    @foreach ($roles as $roleOption)
                        @if (in_array($roleOption->value, $assignableRoles, true))
                            <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                        @endif
                    @endforeach
                </select>
                @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            Adicionar usuário
        </button>
    </form>
</div>
