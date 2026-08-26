<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Categorias</h1>

    @error('delete') <p class="mb-4 text-sm text-red-600">{{ $message }}</p> @enderror

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">Pontos base</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($categories as $category)
                    <tr wire:key="category-{{ $category->id }}">
                        @if ($editingId === $category->id)
                            <td class="px-4 py-2">
                                <input type="text" wire:model="editingName" class="w-full rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" min="0" wire:model="editingBasePoints" class="w-24 rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            </td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" wire:click="update" class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-500">Salvar</button>
                                <button type="button" wire:click="cancelEdit" class="ml-2 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Cancelar</button>
                            </td>
                        @else
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $category->name }}</td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $category->base_points }}</td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" wire:click="edit({{ $category->id }})" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Editar</button>
                                <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="Excluir esta categoria?" class="ml-2 rounded-md border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/30">Excluir</button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <form wire:submit="create" class="mt-6 flex items-end gap-2">
        <div class="flex-1">
            <label for="new-category-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
            <input id="new-category-name" type="text" wire:model="name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="new-category-points" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pontos base</label>
            <input id="new-category-points" type="number" min="0" wire:model="base_points" class="mt-1 block w-32 rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
        </div>

        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            Adicionar categoria
        </button>
    </form>
</div>
