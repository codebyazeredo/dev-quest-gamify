<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Títulos</h1>

        @can('create', \App\Models\Title::class)
            <button type="button" wire:click="toggleCreate" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                + Novo título
            </button>
        @endcan
    </div>

    @error('delete') <p class="mb-4 text-sm text-red-600">{{ $message }}</p> @enderror

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
                @forelse ($titles as $title)
                    <tr wire:key="title-{{ $title->id }}">
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
                            <button type="button" wire:click="edit({{ $title->id }})" title="Editar" aria-label="Editar" class="rounded-md border border-gray-300 p-1.5 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"><x-icon name="pencil" class="h-4 w-4" /></button>
                            <button type="button" wire:click="delete({{ $title->id }})" wire:confirm="Excluir este título?" title="Excluir" aria-label="Excluir" class="ml-2 rounded-md border border-red-300 p-1.5 text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/30"><x-icon name="trash" class="h-4 w-4" /></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Nenhum título cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $titles->links() }}
    </div>

    @if ($showCreateModal)
        <livewire:admin.titles.create wire:key="title-create" />
    @endif

    @if ($editingTitleId)
        <livewire:admin.titles.edit :title-id="$editingTitleId" wire:key="title-edit-{{ $editingTitleId }}" />
    @endif
</div>
