<x-modal title="Editar usuário" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
            <p class="mt-1 text-sm text-gray-800 dark:text-gray-100">{{ $user->name }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
            <input type="email" wire:model="email" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nova senha (opcional)</label>
                <input type="password" wire:model="password" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar senha</label>
                <input type="password" wire:model="password_confirmation" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>
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

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                Cancelar
            </button>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Salvar alterações
            </button>
        </div>
    </form>
</x-modal>
