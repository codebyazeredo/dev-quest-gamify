@props(['task'])

@php
    $bg = $task->category->color;
    $fg = $task->category->text_color;
@endphp

<div
    draggable="true"
    x-on:dragstart="draggingTaskId = {{ $task->id }}"
    style="background-color: {{ $bg }}; color: {{ $fg }}; border-color: {{ $fg }}33;"
    class="cursor-move rounded-xl border p-4 shadow-sm transition-shadow hover:shadow-md"
>
    <div class="flex items-start justify-between gap-2">
        <a href="{{ route('tasks.show', $task) }}" class="text-sm font-medium hover:underline">
            {{ $task->title }}
        </a>

        @can('update', $task)
            <button type="button" x-on:click="$dispatch('open-task-edit', { taskId: {{ $task->id }} })" title="Editar" aria-label="Editar" class="shrink-0 rounded-md p-1 opacity-70 hover:bg-black/10 hover:opacity-100">
                <x-icon name="pencil" class="h-3.5 w-3.5" />
            </button>
        @endcan
    </div>

    <div class="mt-2 flex items-center justify-between text-xs">
        <span class="rounded-full px-2 py-0.5 text-[11px] font-medium" style="background-color: {{ $fg }}1a;">
            {{ $task->category->name }}
        </span>
        <span class="flex items-center gap-1.5 opacity-75">
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
            <span class="text-xs opacity-75">
                Prazo: {{ $task->due_at->format('d/m/Y H:i') }}
            </span>
        @endif
    </div>

    <div class="mt-3 flex items-center justify-between text-xs">
        @if ($task->assignedTo)
            <span class="opacity-75">{{ $task->assignedTo->name }}</span>
        @else
            <span class="opacity-50">Não atribuído</span>
        @endif

        @can('claim', $task)
            <button type="button" wire:click="claim({{ $task->id }})" class="rounded-md px-2 py-0.5 font-medium hover:opacity-80" style="background-color: {{ $fg }}1a;">
                Assumir
            </button>
        @endcan
    </div>
</div>
