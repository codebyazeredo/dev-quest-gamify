<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-ink">Títulos</h1>

        @can('create', \App\Models\Title::class)
            <button type="button" wire:click="toggleCreate" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover">
                + Novo título
            </button>
        @endcan
    </div>

    @error('delete') <p class="mb-4 text-sm text-terracotta">{{ $message }}</p> @enderror

    <div class="overflow-x-auto rounded-xl border border-line bg-card">
        <table class="w-full text-sm">
            <thead class="bg-line/20 text-left text-ink-muted">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Conquista</th>
                    <th class="px-4 py-3">Ativo</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line/50">
                @forelse ($titles as $title)
                    <tr wire:key="title-{{ $title->id }}">
                        <td class="flex items-center gap-2 px-4 py-3 text-ink">
                            <span class="text-gold"><x-icon :name="$title->icon" class="h-4 w-4" /></span>
                            {{ $title->name }}
                        </td>
                        <td class="px-4 py-3 text-ink">{{ $title->achievement?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($title->active)
                                <x-badge color="forest">Ativo</x-badge>
                            @else
                                <x-badge>Inativo</x-badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="edit({{ $title->id }})" title="Editar" aria-label="Editar" class="rounded-lg border border-line p-1.5 text-ink-muted hover:bg-line/20"><x-icon name="pencil" class="h-4 w-4" /></button>
                            <button type="button" wire:click="delete({{ $title->id }})" wire:confirm="Excluir este título?" title="Excluir" aria-label="Excluir" class="ml-2 rounded-lg border border-terracotta/30 p-1.5 text-terracotta hover:bg-terracotta/10"><x-icon name="trash" class="h-4 w-4" /></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-ink-muted">Nenhum título cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $titles->links() }}
    </div>

    @if ($showCreateModal)
        <livewire:admin.titles.create wire:key="title-create" />
    @endif

    @if ($editingTitleId)
        <livewire:admin.titles.edit :title-id="$editingTitleId" wire:key="title-edit-{{ $editingTitleId }}" />
    @endif
</div>
