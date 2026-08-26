<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Quadros</h1>

        @can('create', \App\Models\Board::class)
            <button type="button" wire:click="toggleCreate" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                + Novo quadro
            </button>
        @endcan
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($boards as $board)
            <a href="{{ route('boards.show', $board) }}" class="block rounded-lg border bg-white p-4 shadow-sm hover:border-indigo-400 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $board->name }}</h2>
                    @unless ($board->is_active)
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">Inativo</span>
                    @endunless
                </div>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $board->description }}</p>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ $board->tasks_count }} tarefas</p>
            </a>
        @endforeach
    </div>

    @if ($showCreateModal)
        <livewire:board.create wire:key="board-create" />
    @endif
</div>
