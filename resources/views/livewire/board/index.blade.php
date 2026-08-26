<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-ink">Quadros</h1>

        @can('create', \App\Models\Board::class)
            <button type="button" wire:click="toggleCreate" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover">
                + Novo quadro
            </button>
        @endcan
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($boards as $board)
            <a href="{{ route('boards.show', $board) }}" class="block rounded-xl border border-line bg-card p-4 shadow-sm hover:border-primary transition-colors">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-ink">{{ $board->name }}</h2>
                    @unless ($board->is_active)
                        <x-badge>Inativo</x-badge>
                    @endunless
                </div>
                <p class="mt-1 text-sm text-ink-muted">{{ $board->description }}</p>
                <p class="mt-3 text-xs text-ink-muted">{{ $board->tasks_count }} tarefas</p>
            </a>
        @endforeach
    </div>

    @if ($showCreateModal)
        <livewire:board.create wire:key="board-create" />
    @endif
</div>
