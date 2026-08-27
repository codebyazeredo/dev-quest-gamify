<div>
    <x-page-header title="Desafios" :back="route('admin.index')" backLabel="Configurações">
        @can('create', \App\Models\Challenge::class)
            <button type="button" wire:click="toggleCreate" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover">
                + Novo desafio
            </button>
        @endcan
    </x-page-header>

    @error('delete') <p class="mb-4 text-sm text-terracotta">{{ $message }}</p> @enderror

    <div class="overflow-x-auto rounded-xl border border-line bg-card">
        <table class="w-full text-sm">
            <thead class="bg-line/20 text-left text-ink-muted">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Meta</th>
                    <th class="px-4 py-3">XP</th>
                    <th class="px-4 py-3">Janela</th>
                    <th class="px-4 py-3">Ativo</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line/50">
                @forelse ($challenges as $challenge)
                    <tr wire:key="challenge-{{ $challenge->id }}">
                        <td class="px-4 py-3 text-ink">{{ $challenge->name }}</td>
                        <td class="px-4 py-3 text-ink">{{ $challenge->type->label() }}</td>
                        <td class="px-4 py-3 text-ink">{{ $challenge->target }}</td>
                        <td class="px-4 py-3 text-ink">{{ $challenge->xp_reward }}</td>
                        <td class="px-4 py-3 text-xs text-ink-muted">
                            {{ $challenge->starts_at->format('d/m') }} - {{ $challenge->ends_at->format('d/m') }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($challenge->active)
                                <x-badge color="forest">Ativo</x-badge>
                            @else
                                <x-badge>Inativo</x-badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="edit({{ $challenge->id }})" title="Editar" aria-label="Editar" class="rounded-lg border border-line p-1.5 text-ink-muted hover:bg-line/20"><x-icon name="pencil" class="h-4 w-4" /></button>
                            <button type="button" wire:click="delete({{ $challenge->id }})" wire:confirm="Excluir este desafio?" title="Excluir" aria-label="Excluir" class="ml-2 rounded-lg border border-terracotta/30 p-1.5 text-terracotta hover:bg-terracotta/10"><x-icon name="trash" class="h-4 w-4" /></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-ink-muted">Nenhum desafio cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-per-page-selector />
        {{ $challenges->links() }}
    </div>

    @if ($showCreateModal)
        <livewire:admin.challenges.create wire:key="challenge-create" />
    @endif

    @if ($editingChallengeId)
        <livewire:admin.challenges.edit :challenge-id="$editingChallengeId" wire:key="challenge-edit-{{ $editingChallengeId }}" />
    @endif
</div>
