<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-ink">Gravidade</h1>

        @can('create', \App\Models\TaskPriority::class)
            <button type="button" wire:click="toggleCreate" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover">
                + Nova gravidade
            </button>
        @endcan
    </div>

    @error('delete') <p class="mb-4 text-sm text-terracotta">{{ $message }}</p> @enderror

    <div class="overflow-x-auto rounded-xl border border-line">
        <table class="w-full text-sm">
            <thead class="bg-line/20 text-left text-ink-muted">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Multiplicador</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line/50">
                @forelse ($priorities as $priority)
                    <tr wire:key="priority-{{ $priority->id }}">
                        <td class="px-4 py-3 text-ink">{{ $priority->name }}</td>
                        <td class="px-4 py-3 text-ink">{{ $priority->multiplier }}x</td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="edit({{ $priority->id }})" title="Editar" aria-label="Editar" class="rounded-lg border border-line p-1.5 text-ink-muted hover:bg-line/20"><x-icon name="pencil" class="h-4 w-4" /></button>
                            <button type="button" wire:click="delete({{ $priority->id }})" wire:confirm="Excluir esta gravidade?" title="Excluir" aria-label="Excluir" class="ml-2 rounded-lg border border-terracotta/30 p-1.5 text-terracotta hover:bg-terracotta/10"><x-icon name="trash" class="h-4 w-4" /></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-ink-muted">Nenhuma gravidade cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $priorities->links() }}
    </div>

    @if ($showCreateModal)
        <livewire:admin.priorities.create wire:key="priority-create" />
    @endif

    @if ($editingPriorityId)
        <livewire:admin.priorities.edit :priority-id="$editingPriorityId" wire:key="priority-edit-{{ $editingPriorityId }}" />
    @endif
</div>
