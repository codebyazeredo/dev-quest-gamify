<x-modal title="Editar regra de XP">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Evento</label>
            <p class="mt-1 text-sm text-gray-800 dark:text-gray-100">{{ $rule->type->label() }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ $rule->type->isPercentageBased() ? 'Recompensa (% do valor da tarefa)' : 'Recompensa de XP' }}
            </label>
            <input type="number" min="0" @if ($rule->type->isPercentageBased()) max="100" @endif wire:model="xp_reward" autofocus class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            @error('xp_reward') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
