<div class="flex flex-1 min-h-0 flex-col">
    <div x-data="{ draggingTaskId: null }" class="flex flex-1 min-h-0 gap-4 overflow-x-auto pb-4">
        @foreach ($board->columns as $column)
            <div
                class="flex w-64 flex-shrink-0 flex-col rounded-xl bg-line/10 p-3"
                x-on:dragover.prevent
                x-on:drop="$wire.moveTask(draggingTaskId, {{ $column->id }}, {{ $column->tasks->count() }})"
                wire:key="column-{{ $column->id }}"
            >
                <div class="mb-3 flex shrink-0 items-center justify-between">
                    <h3 class="text-sm font-semibold text-ink">{{ $column->name }}</h3>
                    <span class="rounded-full bg-card px-1.5 py-0.5 text-xs text-ink-muted">{{ $column->tasks->count() }}</span>
                </div>

                <div class="min-h-0 flex-1 space-y-2 overflow-y-auto">
                    @foreach ($column->tasks as $task)
                        <x-task-card :task="$task" wire:key="task-{{ $task->id }}" />
                    @endforeach
                </div>

                @if ($column->status === \App\Enums\TaskStatus::BACKLOG)
                    @can('create', \App\Models\Task::class)
                        <button type="button" wire:click="openCreate({{ $column->id }})" class="mt-2 w-full shrink-0 rounded-lg border border-dashed border-line py-1.5 text-xs font-medium text-ink-muted hover:border-primary hover:text-primary">
                            + Nova tarefa
                        </button>
                    @endcan
                @endif
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
