<div class="flex flex-1 min-h-0 flex-col">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('boards.index') }}" class="inline-flex items-center gap-1 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                &larr; Quadros
            </a>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $board->name }}</h1>
            @if ($board->description)
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $board->description }}</p>
            @endif
        </div>

        @can('update', $board)
            <button type="button" wire:click="toggleEdit" title="Editar quadro" aria-label="Editar quadro" class="rounded-md border p-2 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                <x-icon name="pencil" class="h-4 w-4" />
            </button>
        @endcan
    </div>

    <livewire:task.kanban :board="$board" wire:key="kanban-{{ $board->id }}" />

    @if ($showEditModal)
        <livewire:board.edit :board="$board" wire:key="board-edit-{{ $board->id }}" />
    @endif
</div>
