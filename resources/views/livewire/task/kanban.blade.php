<div>
    <div x-data="{ draggingTaskId: null }" class="flex gap-4 overflow-x-auto pb-4">
        @foreach ($board->columns as $column)
            <div
                class="w-64 flex-shrink-0 rounded-lg bg-gray-50 p-3 dark:bg-gray-900"
                x-on:dragover.prevent
                x-on:drop="$wire.moveTask(draggingTaskId, {{ $column->id }}, {{ $column->tasks->count() }})"
                wire:key="column-{{ $column->id }}"
            >
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $column->name }}</h3>
                    <span class="text-xs text-gray-400">{{ $column->tasks->count() }}</span>
                </div>

                <div class="space-y-2">
                    @foreach ($column->tasks as $task)
                        <x-task-card :task="$task" wire:key="task-{{ $task->id }}" />
                    @endforeach
                </div>

                @if ($column->status === \App\Enums\TaskStatus::BACKLOG)
                    @can('create', \App\Models\Task::class)
                        <button type="button" wire:click="openCreate({{ $column->id }})" class="mt-2 w-full rounded-md border border-dashed border-gray-300 py-1 text-xs text-gray-500 hover:border-indigo-400 hover:text-indigo-600 dark:border-gray-600">
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
