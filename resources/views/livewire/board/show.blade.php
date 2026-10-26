<div class="d-flex flex-column flex-grow-1 min-h-0">
    <x-page-header :title="$board->name" :subtitle="$board->description" :back="route('boards.index')" backLabel="Quadros">
        <x-icon-button :href="route('boards.archive', $board)" icon="archive" label="Arquivo do quadro" />

        @can('update', $board)
            <x-icon-button wire:click="toggleEdit" icon="pencil-square" label="Editar quadro" />
        @endcan
    </x-page-header>

    <livewire:task.kanban :board="$board" wire:key="kanban-{{ $board->id }}" />

    @if ($showEditModal)
        <livewire:board.edit :board="$board" wire:key="board-edit-{{ $board->id }}" />
    @endif
</div>
