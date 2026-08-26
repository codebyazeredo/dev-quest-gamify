<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Gravidade</h1>

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">Gravidade</th>
                    <th class="px-4 py-2">Multiplicador</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($rows as $row)
                    <tr wire:key="priority-{{ $row['priority']->value }}">
                        @if ($editingPriority === $row['priority']->value)
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $row['priority']->label() }}</td>
                            <td class="px-4 py-2">
                                <input type="number" step="0.01" min="0.01" wire:model="editingMultiplier" class="w-24 rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                @error('editingMultiplier') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" wire:click="update" class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-500">Salvar</button>
                                <button type="button" wire:click="cancelEdit" class="ml-2 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Cancelar</button>
                            </td>
                        @else
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $row['priority']->label() }}</td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ number_format($row['multiplier'], 2) }}×</td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" wire:click="edit({{ $row['priority']->value }})" title="Editar" aria-label="Editar" class="rounded-md border border-gray-300 p-1.5 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"><x-icon name="pencil" class="h-4 w-4" /></button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
