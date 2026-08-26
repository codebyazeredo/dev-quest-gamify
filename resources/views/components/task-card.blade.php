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
                Editar
            </button>
        @endcan
    </div>

    <div class="mt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
        <span class="rounded bg-gray-100 px-1.5 py-0.5 dark:bg-gray-700">{{ $task->category->name }}</span>
        <span class="flex items-center gap-1.5">
            {{ $task->priority->label() }}
            <x-xp-badge :amount="$task->xpValue()" />
        </span>
    </div>

    @if ($task->rejection_reason)
        <span class="mt-2 inline-block rounded bg-rose-100 px-1.5 py-0.5 text-xs font-medium text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">
            Reprovada
        </span>
    @endif

    @if ($task->isLate())
        <span class="mt-2 inline-block rounded bg-rose-100 px-1.5 py-0.5 text-xs font-medium text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">
            Atrasada
        </span>
    @elseif ($task->due_at && ! $task->completed_at)
        <span class="mt-2 inline-block text-xs text-gray-400">
            Prazo: {{ $task->due_at->format('d/m/Y H:i') }}
        </span>
    @endif

    <div class="mt-2 flex items-center justify-between text-xs">
        @if ($task->assignedTo)
            <span class="text-gray-600 dark:text-gray-300">{{ $task->assignedTo->name }}</span>
        @else
            <span class="text-gray-400">Não atribuído</span>
        @endif

        @can('claim', $task)
            <button type="button" wire:click="claim({{ $task->id }})" class="rounded bg-indigo-50 px-2 py-0.5 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-300">
                Assumir
            </button>
        @endcan
    </div>
</div>
