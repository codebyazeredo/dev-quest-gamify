<div class="d-flex flex-column flex-grow-1 min-h-0">
    <x-page-header :title="$board->name" :subtitle="$board->description" :back="route('boards.index')" backLabel="Quadros">
        <a href="{{ route('boards.archive', $board) }}" class="btn btn-outline-secondary bg-body">
            <x-icon name="archive" /> Arquivo
        </a>

        @can('update', $board)
            <button type="button" wire:click="toggleEdit" class="btn btn-outline-secondary bg-body">
                <x-icon name="pencil" /> Editar quadro
            </button>
        @endcan
    </x-page-header>

    <livewire:task.kanban :board="$board" wire:key="kanban-{{ $board->id }}" />

    @if ($showEditModal)
        <livewire:board.edit :board="$board" wire:key="board-edit-{{ $board->id }}" />
    @endif
</div>
