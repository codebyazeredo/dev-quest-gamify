<div>
    <x-page-header title="Títulos" subtitle="Títulos desbloqueáveis por conquistas." :back="route('admin.index')" backLabel="Configurações">
        @can('create', \App\Models\Title::class)
            <x-button wire:click="toggleCreate"><x-icon name="plus" /> Novo título</x-button>
        @endcan
    </x-page-header>

    @error('delete') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror

    <x-data-table :paginator="$titles">
        <x-slot:head>
            <th class="ps-4">Nome</th>
            <th>Conquista</th>
            <th>Status</th>
            <th class="text-end pe-4"></th>
        </x-slot:head>

        @forelse ($titles as $title)
            <tr wire:key="title-{{ $title->id }}">
                <td class="ps-4">
                    <div class="d-flex align-items-center gap-2">
                        <x-icon-chip :icon="$title->icon" color="warning" size="sm" />
                        <span class="fw-medium">{{ $title->name }}</span>
                    </div>
                </td>
                <td>{{ $title->achievement?->name ?? '—' }}</td>
                <td>
                    @if ($title->active)
                        <x-badge color="success">Ativo</x-badge>
                    @else
                        <x-badge>Inativo</x-badge>
                    @endif
                </td>
                <td class="text-end pe-4">
                    <x-row-actions :edit="'edit('.$title->id.')'" :delete="'delete('.$title->id.')'" confirm="Excluir este título?" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-body-secondary py-5">Nenhum título cadastrado.</td>
            </tr>
        @endforelse
    </x-data-table>

    @if ($showCreateModal)
        <livewire:admin.titles.create wire:key="title-create" />
    @endif

    @if ($editingTitleId)
        <livewire:admin.titles.edit :title-id="$editingTitleId" wire:key="title-edit-{{ $editingTitleId }}" />
    @endif
</div>
