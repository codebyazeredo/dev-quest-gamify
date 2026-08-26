<div class="flex flex-1 min-h-0 flex-col">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('boards.index') }}" class="inline-flex items-center gap-1 rounded-lg border border-line px-3 py-1.5 text-sm font-medium text-ink hover:bg-line/20">
                &larr; Quadros
            </a>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-ink">{{ $board->name }}</h1>
            @if ($board->description)
                <p class="text-sm text-ink-muted">{{ $board->description }}</p>
            @endif
        </div>

        @can('update', $board)
            <button type="button" wire:click="toggleEdit" title="Editar quadro" aria-label="Editar quadro" class="self-start rounded-lg border border-line p-2 text-ink-muted hover:bg-line/20">
                <x-icon name="pencil" class="h-4 w-4" />
            </button>
        @endcan
    </div>

    <livewire:task.kanban :board="$board" wire:key="kanban-{{ $board->id }}" />

    @if ($showEditModal)
        <livewire:board.edit :board="$board" wire:key="board-edit-{{ $board->id }}" />
    @endif
</div>
