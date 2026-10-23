<div>
    <x-page-header title="Conquistas" subtitle="Condições e recompensas das conquistas." :back="route('admin.index')" backLabel="Configurações">
        @can('create', \App\Models\Achievement::class)
            <x-button wire:click="toggleCreate"><x-icon name="plus" /> Nova conquista</x-button>
        @endcan
    </x-page-header>

    @error('delete') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror

    <x-data-table :paginator="$achievements">
        <x-slot:head>
            <th class="ps-4">Nome</th>
            <th>Condição</th>
            <th>XP</th>
            <th>Status</th>
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
                    @if ($achievement->active)
                        <x-badge color="success">Ativo</x-badge>
                    @else
                        <x-badge>Inativo</x-badge>
                    @endif
                </td>
                <td class="text-end pe-4">
                    <x-row-actions :edit="'edit('.$achievement->id.')'" :delete="'delete('.$achievement->id.')'" confirm="Excluir esta conquista?" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-body-secondary py-5">Nenhuma conquista cadastrada.</td>
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
