@props(['task'])

<div
    draggable="true"
    x-on:dragstart="draggingTaskId = {{ $task->id }}"
    class="cursor-move rounded-md border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800"
>
    <div class="flex items-start justify-between gap-2">
        <a href="{{ route('tasks.show', $task) }}" class="text-sm font-medium text-gray-800 hover:underline dark:text-gray-100">
            {{ $task->title }}
        </a>

        @can('update', $task)
            <button type="button" x-on:click="$dispatch('open-task-edit', { taskId: {{ $task->id }} })" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                Edit
            </button>
        @endcan
    </div>

    <div class="mt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
        <span class="rounded bg-gray-100 px-1.5 py-0.5 dark:bg-gray-700">{{ $task->category->name }}</span>
        <span>{{ $task->priority->label() }}</span>
    </div>

    <div class="mt-2 flex items-center justify-between text-xs">
        @if ($task->assignedTo)
            <span class="text-gray-600 dark:text-gray-300">{{ $task->assignedTo->name }}</span>
        @else
            <span class="text-gray-400">Unassigned</span>
        @endif

        @can('claim', $task)
            <button type="button" wire:click="claim({{ $task->id }})" class="rounded bg-indigo-50 px-2 py-0.5 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-300">
                Claim
            </button>
        @endcan
    </div>
</div>
