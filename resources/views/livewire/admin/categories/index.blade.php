<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-ink">Categorias</h1>

        @can('create', \App\Models\TaskCategory::class)
            <button type="button" wire:click="toggleCreate" class="self-start rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover">
                + Nova categoria
            </button>
        @endcan
    </div>

    @error('delete') <p class="mb-4 text-sm text-terracotta">{{ $message }}</p> @enderror

    <div class="overflow-x-auto rounded-xl border border-line bg-card">
        <table class="w-full text-sm">
            <thead class="bg-line/20 text-left text-ink-muted">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Pontos base</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line/50">
                @forelse ($categories as $category)
                    <tr wire:key="category-{{ $category->id }}">
                        <td class="px-4 py-3 text-ink">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-ink">{{ $category->base_points }}</td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="edit({{ $category->id }})" title="Editar" aria-label="Editar" class="rounded-lg border border-line p-1.5 text-ink-muted hover:bg-line/20"><x-icon name="pencil" class="h-4 w-4" /></button>
                            <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="Excluir esta categoria?" title="Excluir" aria-label="Excluir" class="ml-2 rounded-lg border border-terracotta/30 p-1.5 text-terracotta hover:bg-terracotta/10"><x-icon name="trash" class="h-4 w-4" /></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-ink-muted">Nenhuma categoria cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>

    @if ($showCreateModal)
        <livewire:admin.categories.create wire:key="category-create" />
    @endif

    @if ($editingCategoryId)
        <livewire:admin.categories.edit :category-id="$editingCategoryId" wire:key="category-edit-{{ $editingCategoryId }}" />
    @endif
</div>
