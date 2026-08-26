<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Roles &amp; permissões</h1>

    @error('delete') <p class="mb-4 text-sm text-red-600">{{ $message }}</p> @enderror

    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">Role</th>
                    @foreach ($permissions as $permission)
                        <th class="px-3 py-2 text-center">{{ $permission->name }}</th>
                    @endforeach
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($roles as $role)
                    <tr wire:key="role-{{ $role->id }}">
                        <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100">
                            {{ \App\Enums\UserRole::labelFor($role->name) }}
                            @if ($role->name === 'admin')
                                <span class="ml-1 text-xs text-gray-400">(acesso total)</span>
                            @endif
                        </td>
                        @foreach ($permissions as $permission)
                            <td class="px-3 py-2 text-center">
                                <input type="checkbox"
                                    @checked($role->hasPermissionTo($permission))
                                    @disabled($role->name === 'admin')
                                    wire:click="togglePermission({{ $role->id }}, {{ $permission->id }})"
                                    class="rounded border-gray-300">
                            </td>
                        @endforeach
                        <td class="px-4 py-2 text-right">
                            @unless ($role->name === 'admin')
                                <button type="button" wire:click="delete({{ $role->id }})" wire:confirm="Excluir este role?"
                                    class="rounded-md border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/30">
                                    Excluir
                                </button>
                            @endunless
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <form wire:submit="create" class="mt-6 flex items-end gap-2">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Novo role</label>
            <input type="text" wire:model="name" placeholder="ex: financeiro" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            Criar role
        </button>
    </form>
</div>
