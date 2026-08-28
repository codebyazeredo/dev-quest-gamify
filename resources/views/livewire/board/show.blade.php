<div class="flex flex-1 min-h-0 flex-col">
    <x-page-header :title="$board->name" :subtitle="$board->description" :back="route('boards.index')" backLabel="Quadros">
        <a href="{{ route('boards.archive', $board) }}" title="Arquivo" aria-label="Arquivo" class="rounded-lg border border-line p-2 text-ink-muted hover:bg-line/20">
            <x-icon name="archive" class="h-4 w-4" />
        </a>

        @can('update', $board)
            <button type="button" wire:click="toggleEdit" title="Editar quadro" aria-label="Editar quadro" class="rounded-lg border border-line p-2 text-ink-muted hover:bg-line/20">
                <x-icon name="pencil" class="h-4 w-4" />
            </button>
        @endcan
    </x-page-header>

    <livewire:task.kanban :board="$board" wire:key="kanban-{{ $board->id }}" />

    @if ($showEditModal)
        <livewire:board.edit :board="$board" wire:key="board-edit-{{ $board->id }}" />
    @endif
</div>
