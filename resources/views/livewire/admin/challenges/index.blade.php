<div>
    <x-page-header title="Desafios" subtitle="Metas por período com recompensa em XP." :back="route('admin.index')" backLabel="Configurações">
        @can('create', \App\Models\Challenge::class)
            <x-button wire:click="toggleCreate"><x-icon name="plus" /> Novo desafio</x-button>
        @endcan
    </x-page-header>

    @error('delete') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror

    <x-data-table :paginator="$challenges">
        <x-slot:head>
            <th class="ps-4">Nome</th>
            <th>Tipo</th>
            <th>Meta</th>
            <th>XP</th>
            <th>Janela</th>
            <th>Status</th>
            <th class="text-end pe-4"></th>
        </x-slot:head>

        @forelse ($challenges as $challenge)
            <tr wire:key="challenge-{{ $challenge->id }}">
                <td class="ps-4 fw-medium">{{ $challenge->name }}</td>
                <td>{{ $challenge->type->label() }}</td>
                <td>{{ $challenge->target }}</td>
                <td>{{ $challenge->xp_reward }}</td>
                <td class="small text-body-secondary text-nowrap">
                    {{ $challenge->starts_at->format('d/m') }} – {{ $challenge->ends_at->format('d/m') }}
                </td>
                <td>
                    @if ($challenge->active)
                        <x-badge color="success">Ativo</x-badge>
                    @else
                        <x-badge>Inativo</x-badge>
                    @endif
                </td>
                <td class="text-end pe-4">
                    <x-row-actions :edit="'edit('.$challenge->id.')'" :delete="'delete('.$challenge->id.')'" confirm="Excluir este desafio?" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-body-secondary py-5">Nenhum desafio cadastrado.</td>
            </tr>
        @endforelse
    </x-data-table>

    @if ($showCreateModal)
        <livewire:admin.challenges.create wire:key="challenge-create" />
    @endif

    @if ($editingChallengeId)
        <livewire:admin.challenges.edit :challenge-id="$editingChallengeId" wire:key="challenge-edit-{{ $editingChallengeId }}" />
    @endif
</div>
