@props(['task'])

<div
    draggable="true"
    x-on:dragstart="draggingTaskId = {{ $task->id }}"
    class="cursor-move rounded-xl border border-line/60 bg-card p-4 shadow-sm transition-shadow hover:shadow-md"
>
    <div class="flex items-start justify-between gap-2">
        <a href="{{ route('tasks.show', $task) }}" class="text-sm font-medium text-ink hover:underline">
            {{ $task->title }}
        </a>

        @can('update', $task)
            <button type="button" x-on:click="$dispatch('open-task-edit', { taskId: {{ $task->id }} })" title="Editar" aria-label="Editar" class="shrink-0 rounded-md p-1 text-ink-muted hover:bg-line/30">
                <x-icon name="pencil" class="h-3.5 w-3.5" />
            </button>
        @endcan
    </div>

    <div class="mt-2 flex items-center justify-between text-xs">
        <x-badge>{{ $task->category->name }}</x-badge>
        <span class="flex items-center gap-1.5 text-ink-muted">
            {{ $task->priority->name }}
            <x-xp-badge :amount="$task->xpValue()" />
        </span>
    </div>

    <div class="mt-2 flex flex-wrap gap-1.5">
        @if ($task->rejection_reason)
            <x-badge color="terracotta">Reprovada</x-badge>
        @endif

        @if ($task->status === \App\Enums\TaskStatus::APPROVED && ! $task->taskEvents->contains('type', \App\Enums\TaskEventType::HOMOLOGATION_COMPLETED))
            <x-badge color="amber-clay">Não homologado</x-badge>
        @endif

        @if ($task->status === \App\Enums\TaskStatus::DONE && ! $task->taskEvents->contains('type', \App\Enums\TaskEventType::DEPLOYED))
            <x-badge color="amber-clay">Não implantado</x-badge>
        @endif

        @if ($task->isLate())
            <x-badge color="terracotta">Atrasada</x-badge>
        @elseif ($task->due_at && ! $task->completed_at)
            <span class="text-xs text-ink-muted">
                Prazo: {{ $task->due_at->format('d/m/Y H:i') }}
            </span>
        @endif
    </div>

    <div class="mt-3 flex items-center justify-between text-xs">
        @if ($task->assignedTo)
            <span class="text-ink-muted">{{ $task->assignedTo->name }}</span>
        @else
            <span class="text-ink-muted/60">Não atribuído</span>
        @endif

        @can('claim', $task)
            <button type="button" wire:click="claim({{ $task->id }})" class="rounded-md bg-primary/10 px-2 py-0.5 font-medium text-primary hover:bg-primary/20">
                Assumir
            </button>
        @endcan
    </div>
</div>
