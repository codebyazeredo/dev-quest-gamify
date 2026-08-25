<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Títulos</h1>

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">Conquista</th>
                    <th class="px-4 py-2">Ativo</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($titles as $title)
                    <tr wire:key="title-{{ $title->id }}">
                        @if ($editingId === $title->id)
                            <td class="px-4 py-2">
                                <input type="text" wire:model="editingName" class="w-full rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            </td>
                            <td class="px-4 py-2">
                                <select wire:model="editingAchievementId" class="rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                    <option value="">Nenhuma</option>
                                    @foreach ($achievements as $achievement)
                                        <option value="{{ $achievement->id }}">{{ $achievement->name }}</option>
                                    @endforeach
                                </select>
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
                                <span class="text-amber-500"><x-icon :name="$title->icon" class="h-4 w-4" /></span>
                                {{ $title->name }}
                            </td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $title->achievement?->name ?? '—' }}</td>
                            <td class="px-4 py-2">
                                @if ($title->active)
                                    <span class="text-green-600">Ativo</span>
                                @else
                                    <span class="text-gray-400">Inativo</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" wire:click="edit({{ $title->id }})" class="text-indigo-600 hover:underline">Editar</button>
                                <button type="button" wire:click="delete({{ $title->id }})" wire:confirm="Excluir este título?" class="ml-2 text-red-600 hover:underline">Excluir</button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @error('delete') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

    <form wire:submit="create" class="mt-6 flex items-end gap-2">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
            <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ícone</label>
            <input type="text" wire:model="icon" class="mt-1 block w-24 rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Conquista</label>
            <select wire:model="achievement_id" class="mt-1 block rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                <option value="">Nenhuma</option>
                @foreach ($achievements as $achievement)
                    <option value="{{ $achievement->id }}">{{ $achievement->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            Adicionar título
        </button>
    </form>
</div>
