<div>
    <x-page-header title="Títulos" subtitle="Títulos desbloqueáveis por conquistas e desafios." :back="route('admin.index')" backLabel="Configurações">
        @can('create', \App\Models\Title::class)
            <x-button wire:click="toggleCreate"><x-icon name="plus" /> Novo título</x-button>
        @endcan
    </x-page-header>

    @error('delete') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror

    <div class="d-flex flex-wrap gap-2 mb-3">
        <div class="input-group" style="max-width: 18rem;">
            <span class="input-group-text bg-body-tertiary"><i class="bi bi-search" aria-hidden="true"></i></span>
            <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Buscar títulos" aria-label="Buscar títulos">
        </div>

        <select wire:model.live="unlockType" class="form-select w-auto" aria-label="Regra de desbloqueio">
            <option value="">Todas as regras</option>
            @foreach ($unlockTypes as $unlockOption)
                <option value="{{ $unlockOption->value }}">{{ $unlockOption->label() }}</option>
            @endforeach
        </select>

        <select wire:model.live="status" class="form-select w-auto" aria-label="Situação">
            <option value="">Ativos e inativos</option>
            <option value="active">Somente ativos</option>
            <option value="inactive">Somente inativos</option>
        </select>
    </div>

    <x-data-table :paginator="$titles">
        <x-slot:head>
            <th class="ps-4">Título</th>
            <th>Como desbloquear</th>
            <th>Desbloqueios</th>
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
                <td class="text-body-secondary">{{ $title->unlockRuleDescription() }}</td>
                <td>{{ $title->user_titles_count }}</td>
                <td>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" aria-label="Ativo" @checked($title->active) wire:click="toggleActive({{ $title->id }})">
                    </div>
                </td>
                <td class="text-end pe-4">
                    <x-row-actions :edit="'edit('.$title->id.')'" :delete="'delete('.$title->id.')'" confirm="Excluir este título?" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-body-secondary py-5">Nenhum título encontrado.</td>
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
