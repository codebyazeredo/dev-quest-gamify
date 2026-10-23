<div>
    <x-page-header title="Categorias" subtitle="Tipos de tarefa e as cores exibidas no Kanban." :back="route('admin.index')" backLabel="Configurações">
        @can('create', \App\Models\TaskCategory::class)
            <x-button wire:click="toggleCreate"><x-icon name="plus" /> Nova categoria</x-button>
        @endcan
    </x-page-header>

    @error('delete') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror

    <x-data-table :paginator="$categories">
        <x-slot:head>
            <th class="ps-4">Categoria</th>
            <th>Pontos base</th>
            <th class="text-end pe-4"></th>
        </x-slot:head>

        @forelse ($categories as $category)
            <tr wire:key="category-{{ $category->id }}">
                <td class="ps-4">
                    <span class="badge rounded-pill fw-medium px-3 py-2" style="background-color: {{ $category->color }}; color: {{ $category->text_color }};">
                        {{ $category->name }}
                    </span>
                </td>
                <td>{{ $category->base_points }}</td>
                <td class="text-end pe-4">
                    <x-row-actions :edit="'edit('.$category->id.')'" :delete="'delete('.$category->id.')'" confirm="Excluir esta categoria?" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-body-secondary py-5">Nenhuma categoria cadastrada.</td>
            </tr>
        @endforelse
    </x-data-table>

    @if ($showCreateModal)
        <livewire:admin.categories.create wire:key="category-create" />
    @endif

    @if ($editingCategoryId)
        <livewire:admin.categories.edit :category-id="$editingCategoryId" wire:key="category-edit-{{ $editingCategoryId }}" />
    @endif
</div>
