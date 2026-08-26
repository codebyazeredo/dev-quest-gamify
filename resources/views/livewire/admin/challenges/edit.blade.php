<x-modal title="Editar desafio" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                <input type="text" wire:model="name" autofocus class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo</label>
                <select wire:model="type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    @foreach ($challengeTypes as $challengeType)
                        <option value="{{ $challengeType->value }}">{{ $challengeType->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
            <textarea wire:model="description" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
        </div>

        <div class="grid grid-cols-4 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta</label>
                <input type="number" min="1" wire:model="target" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recompensa de XP</label>
                <input type="number" min="0" wire:model="xp_reward" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Início</label>
                <input type="datetime-local" wire:model="starts_at" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fim</label>
                <input type="datetime-local" wire:model="ends_at" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('ends_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
            <input type="checkbox" wire:model="active" class="rounded border-gray-300">
            Ativo
        </label>

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
