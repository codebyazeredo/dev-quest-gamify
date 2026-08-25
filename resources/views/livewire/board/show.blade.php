<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('boards.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Boards</a>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $board->name }}</h1>
            @if ($board->description)
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $board->description }}</p>
            @endif
        </div>

        @can('update', $board)
            <button type="button" wire:click="toggleEdit" class="rounded-md border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                Edit board
            </button>
        @endcan
    </div>

    <livewire:task.kanban :board="$board" wire:key="kanban-{{ $board->id }}" />

    @if ($showEditModal)
        <livewire:board.edit :board="$board" wire:key="board-edit-{{ $board->id }}" />
    @endif
</div>
