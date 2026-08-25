<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Conquistas</h1>

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">Condição</th>
                    <th class="px-4 py-2">XP</th>
                    <th class="px-4 py-2">Ativo</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($achievements as $achievement)
                    <tr wire:key="achievement-{{ $achievement->id }}">
                        @if ($editingId === $achievement->id)
                            <td class="px-4 py-2">
                                <input type="text" wire:model="editingName" class="w-full rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            </td>
                            <td class="px-4 py-2">
                                <select wire:model="editingConditionType" class="rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                    @foreach ($conditionTypes as $type)
                                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                                <input type="number" min="1" wire:model="editingConditionValue" class="w-16 rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" min="0" wire:model="editingXpReward" class="w-20 rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            </td>
                            <td class="px-4 py-2">
                                <input type="checkbox" wire:model="editingActive" class="rounded border-gray-300">
                            </td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" wire:click="update" class="text-indigo-600 hover:underline">Salvar</button>
                                <button type="button" wire:click="cancelEdit" class="ml-2 text-gray-500 hover:underline">Cancelar</button>
                            </td>
                        @else
                            <td class="flex items-center gap-2 px-4 py-2 text-gray-800 dark:text-gray-100">
                                <span class="text-amber-500"><x-icon :name="$achievement->icon" class="h-4 w-4" /></span>
                                {{ $achievement->name }}
                            </td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $achievement->condition_type->label() }} ({{ $achievement->condition_value }})</td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $achievement->xp_reward }}</td>
                            <td class="px-4 py-2">
                                @if ($achievement->active)
                                    <span class="text-green-600">Ativo</span>
                                @else
                                    <span class="text-gray-400">Inativo</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" wire:click="edit({{ $achievement->id }})" class="text-indigo-600 hover:underline">Editar</button>
                                <button type="button" wire:click="delete({{ $achievement->id }})" wire:confirm="Excluir esta conquista?" class="ml-2 text-red-600 hover:underline">Excluir</button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @error('delete') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

    <form wire:submit="create" class="mt-6 space-y-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ícone</label>
                <input type="text" wire:model="icon" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
            <textarea wire:model="description" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
        </div>

        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Condição</label>
                <select wire:model="condition_type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    @foreach ($conditionTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valor</label>
                <input type="number" min="1" wire:model="condition_value" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recompensa de XP</label>
                <input type="number" min="0" wire:model="xp_reward" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>
        </div>

        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            Adicionar conquista
        </button>
    </form>
</div>
