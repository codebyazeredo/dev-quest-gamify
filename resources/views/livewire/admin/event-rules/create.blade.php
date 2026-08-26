<x-modal title="Nova regra de XP">
    <form wire:submit="save" class="space-y-4">
        @if (empty($availableTypes))
            <p class="text-sm text-gray-500 dark:text-gray-400">Todos os eventos já possuem uma regra configurada.</p>
        @else
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Evento</label>
                <select wire:model="type" autofocus class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">Selecione...</option>
                    @foreach ($availableTypes as $availableType)
                        <option value="{{ $availableType->value }}">{{ $availableType->label() }}</option>
                    @endforeach
                </select>
                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recompensa de XP</label>
                <input type="number" min="0" wire:model="xp_reward" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('xp_reward') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <input type="checkbox" wire:model="active" class="rounded border-gray-300">
                Ativo
            </label>
        @endif

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                Cancelar
            </button>
            @if (! empty($availableTypes))
                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Criar regra
                </button>
            @endif
        </div>
    </form>
</x-modal>
