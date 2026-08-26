<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-ink">Conquistas</h1>

        @can('create', \App\Models\Achievement::class)
            <button type="button" wire:click="toggleCreate" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover">
                + Nova conquista
            </button>
        @endcan
    </div>

    @error('delete') <p class="mb-4 text-sm text-terracotta">{{ $message }}</p> @enderror

    <div class="overflow-x-auto rounded-xl border border-line">
        <table class="w-full text-sm">
            <thead class="bg-line/20 text-left text-ink-muted">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Condição</th>
                    <th class="px-4 py-3">XP</th>
                    <th class="px-4 py-3">Ativo</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line/50">
                @forelse ($achievements as $achievement)
                    <tr wire:key="achievement-{{ $achievement->id }}">
                        <td class="flex items-center gap-2 px-4 py-3 text-ink">
                            <span class="text-gold"><x-icon :name="$achievement->icon" class="h-4 w-4" /></span>
                            {{ $achievement->name }}
                        </td>
                        <td class="px-4 py-3 text-ink">{{ $achievement->condition_type->label() }} ({{ $achievement->condition_value }})</td>
                        <td class="px-4 py-3 text-ink">{{ $achievement->xp_reward }}</td>
                        <td class="px-4 py-3">
                            @if ($achievement->active)
                                <x-badge color="forest">Ativo</x-badge>
                            @else
                                <x-badge>Inativo</x-badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="edit({{ $achievement->id }})" title="Editar" aria-label="Editar" class="rounded-lg border border-line p-1.5 text-ink-muted hover:bg-line/20"><x-icon name="pencil" class="h-4 w-4" /></button>
                            <button type="button" wire:click="delete({{ $achievement->id }})" wire:confirm="Excluir esta conquista?" title="Excluir" aria-label="Excluir" class="ml-2 rounded-lg border border-terracotta/30 p-1.5 text-terracotta hover:bg-terracotta/10"><x-icon name="trash" class="h-4 w-4" /></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-ink-muted">Nenhuma conquista cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $achievements->links() }}
    </div>

    @if ($showCreateModal)
        <livewire:admin.achievements.create wire:key="achievement-create" />
    @endif

    @if ($editingAchievementId)
        <livewire:admin.achievements.edit :achievement-id="$editingAchievementId" wire:key="achievement-edit-{{ $editingAchievementId }}" />
    @endif
</div>
