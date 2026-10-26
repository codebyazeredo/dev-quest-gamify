<div>
    <x-page-header title="Desafios" subtitle="Metas por período com recompensa em XP e títulos." :back="route('admin.index')" backLabel="Configurações">
        @can('create', \App\Models\Challenge::class)
            <x-button wire:click="toggleCreate"><x-icon name="plus" /> Novo desafio</x-button>
        @endcan
    </x-page-header>

    @error('delete') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror

    <div class="d-flex flex-wrap gap-2 mb-3">
        <div class="input-group" style="max-width: 18rem;">
            <span class="input-group-text bg-body-tertiary"><i class="bi bi-search" aria-hidden="true"></i></span>
            <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Buscar desafios" aria-label="Buscar desafios">
        </div>

        <select wire:model.live="status" class="form-select w-auto" aria-label="Situação">
            <option value="">Todas as situações</option>
            <option value="running">Em andamento</option>
            <option value="scheduled">Agendados</option>
            <option value="ended">Encerrados</option>
            <option value="inactive">Inativos</option>
        </select>
    </div>

    <x-data-table :paginator="$challenges">
        <x-slot:head>
            <th class="ps-4">Desafio</th>
            <th>Meta</th>
            <th>XP</th>
            <th>Título</th>
            <th>Participação</th>
            <th>Situação</th>
            <th class="text-end pe-4"></th>
        </x-slot:head>

        @forelse ($challenges as $challenge)
            <tr wire:key="challenge-{{ $challenge->id }}">
                <td class="ps-4">
                    <div class="d-flex align-items-center gap-2">
                        <x-icon-chip :icon="$challenge->icon ?? 'flag'" size="sm" />
                        <div class="min-w-0">
                            <div class="fw-medium">{{ $challenge->name }}</div>
                            <div class="small text-body-secondary">
                                {{ $challenge->type->label() }} · {{ $challenge->starts_at->format('d/m') }} – {{ $challenge->ends_at->format('d/m') }}
                            </div>
                        </div>
                    </div>
                </td>
                <td>{{ $challenge->target }}</td>
                <td>{{ $challenge->xp_reward }}</td>
                <td>
                    @forelse ($challenge->titles as $rewardTitle)
                        <x-badge color="warning"><x-icon :name="$rewardTitle->icon ?? 'medal'" /> {{ $rewardTitle->name }}</x-badge>
                    @empty
                        <span class="text-body-tertiary">—</span>
                    @endforelse
                </td>
                <td class="small">
                    {{ $challenge->completions_count }} / {{ $challenge->participants_count }}
                    <span class="text-body-secondary">concluíram</span>
                </td>
                <td>
                    <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                        <input class="form-check-input" type="checkbox" role="switch" aria-label="Ativo" @checked($challenge->active) wire:click="toggleActive({{ $challenge->id }})">
                        <x-badge :color="$challenge->statusColor()">{{ $challenge->statusLabel() }}</x-badge>
                    </div>
                </td>
                <td class="text-end pe-4 text-nowrap">
                    <button type="button" wire:click="duplicate({{ $challenge->id }})" title="Duplicar para o próximo período" aria-label="Duplicar" class="btn btn-ghost btn-icon btn-icon-sm">
                        <i class="bi bi-copy" aria-hidden="true"></i>
                    </button>
                    <x-row-actions :edit="'edit('.$challenge->id.')'" :delete="'delete('.$challenge->id.')'" confirm="Excluir este desafio?" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-body-secondary py-5">Nenhum desafio encontrado.</td>
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
