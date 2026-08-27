<div class="flex flex-1 min-h-0 flex-col">
    @php
        $backlogColumn = $board->columns->firstWhere('status', \App\Enums\TaskStatus::BACKLOG);
    @endphp

    @can('create', \App\Models\Task::class)
        @if ($backlogColumn)
            <div class="mb-3 flex shrink-0 justify-end">
                <button type="button" wire:click="openCreate({{ $backlogColumn->id }})" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
                    + Nova tarefa
                </button>
            </div>
        @endif
    @endcan

    <div x-data="{ draggingTaskId: null }" class="flex flex-1 min-h-0 gap-4 overflow-x-auto pb-4">
        @foreach ($board->columns as $column)
            <div
                class="flex w-64 flex-shrink-0 flex-col rounded-xl border border-line bg-line/20 p-3"
                x-on:dragover.prevent
                x-on:drop="$wire.moveTask(draggingTaskId, {{ $column->id }}, {{ $column->tasks->count() }})"
                wire:key="column-{{ $column->id }}"
            >
                <div class="mb-3 flex shrink-0 items-center justify-between border-b border-line pb-2">
                    <h3 class="text-sm font-semibold text-ink">{{ $column->name }}</h3>
                    <span class="rounded-full bg-card px-1.5 py-0.5 text-xs text-ink-muted">{{ $column->tasks->count() }}</span>
                </div>

                <div class="min-h-0 flex-1 space-y-2 overflow-y-auto">
                    @foreach ($column->tasks as $task)
                        <x-task-card :task="$task" wire:key="task-{{ $task->id }}" />
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @if ($creatingInColumnId)
        <livewire:task.create :board="$board" :column-id="$creatingInColumnId" wire:key="task-create-{{ $creatingInColumnId }}" />
    @endif

    @if ($editingTaskId)
        <livewire:task.edit :task-id="$editingTaskId" wire:key="task-edit-{{ $editingTaskId }}" />
    @endif
</div>
