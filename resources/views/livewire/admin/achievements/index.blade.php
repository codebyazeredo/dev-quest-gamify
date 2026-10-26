<div>
    <x-page-header title="Conquistas" subtitle="Condições, recompensas e títulos das conquistas." :back="route('admin.index')" backLabel="Configurações">
        @can('create', \App\Models\Achievement::class)
            <x-button wire:click="toggleCreate"><x-icon name="plus" /> Nova conquista</x-button>
        @endcan
    </x-page-header>

    @error('delete') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror

    <div class="d-flex flex-wrap gap-2 mb-3">
        <div class="input-group" style="max-width: 18rem;">
            <span class="input-group-text bg-body-tertiary"><i class="bi bi-search" aria-hidden="true"></i></span>
            <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Buscar conquistas" aria-label="Buscar conquistas">
        </div>

        <select wire:model.live="status" class="form-select w-auto" aria-label="Situação">
            <option value="">Ativas e inativas</option>
            <option value="active">Somente ativas</option>
            <option value="inactive">Somente inativas</option>
        </select>
    </div>

    <x-data-table :paginator="$achievements">
        <x-slot:head>
            <th class="ps-4">Conquista</th>
            <th>Condição</th>
            <th>XP</th>
            <th>Título</th>
            <th>Desbloqueios</th>
            <th>Ativa</th>
            <th class="text-end pe-4"></th>
        </x-slot:head>

        @forelse ($achievements as $achievement)
            <tr wire:key="achievement-{{ $achievement->id }}">
                <td class="ps-4">
                    <div class="d-flex align-items-center gap-2">
                        <x-icon-chip :icon="$achievement->icon" color="warning" size="sm" />
                        <span class="fw-medium">{{ $achievement->name }}</span>
                    </div>
                </td>
                <td>{{ $achievement->condition_type->label() }} ({{ $achievement->condition_value }})</td>
                <td>{{ $achievement->xp_reward }}</td>
                <td>
                    @if ($achievement->title)
                        <x-badge color="warning"><x-icon :name="$achievement->title->icon ?? 'medal'" /> {{ $achievement->title->name }}</x-badge>
                    @else
                        <span class="text-body-tertiary">—</span>
                    @endif
                </td>
                <td>{{ $achievement->user_achievements_count }}</td>
                <td>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" aria-label="Ativa" @checked($achievement->active) wire:click="toggleActive({{ $achievement->id }})">
                    </div>
                </td>
                <td class="text-end pe-4 text-nowrap">
                    <button type="button" wire:click="duplicate({{ $achievement->id }})" title="Duplicar" aria-label="Duplicar" class="btn btn-outline-secondary btn-icon btn-icon-sm">
                        <i class="bi bi-copy" aria-hidden="true"></i>
                    </button>
                    <x-row-actions :edit="'edit('.$achievement->id.')'" :delete="'delete('.$achievement->id.')'" confirm="Excluir esta conquista?" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-body-secondary py-5">Nenhuma conquista encontrada.</td>
            </tr>
        @endforelse
    </x-data-table>

    @if ($showCreateModal)
        <livewire:admin.achievements.create wire:key="achievement-create" />
    @endif

    @if ($editingAchievementId)
        <livewire:admin.achievements.edit :achievement-id="$editingAchievementId" wire:key="achievement-edit-{{ $editingAchievementId }}" />
    @endif
</div>
