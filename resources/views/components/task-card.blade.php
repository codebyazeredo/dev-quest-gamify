@props(['task', 'readOnly' => false])

@php
    $categoryBackground = $task->category->color;
    $categoryText = $task->category->text_color;
    $notApprovedByHomologation = $task->status === \App\Enums\TaskStatus::APPROVED
        && ! $task->taskEvents->contains('type', \App\Enums\TaskEventType::HOMOLOGATION_COMPLETED);
    $notDeployed = $task->status === \App\Enums\TaskStatus::DONE
        && ! $task->taskEvents->contains('type', \App\Enums\TaskEventType::DEPLOYED);
@endphp

<div
    @unless ($readOnly)
        draggable="true"
        x-on:dragstart="draggingTaskId = {{ $task->id }}"
        x-on:dragend="draggingTaskId = null; overColumnId = null"
        :class="{ 'kanban-card--dragging': draggingTaskId === {{ $task->id }} }"
    @endunless
    style="--card-accent: {{ $categoryBackground }};"
    class="card kanban-card"
>
    <div class="card-body p-3">
        <div class="d-flex align-items-start justify-content-between gap-2">
            <span class="badge rounded-pill fw-medium" style="background-color: {{ $categoryBackground }}; color: {{ $categoryText }};">
                {{ $task->category->name }}
            </span>

            @unless ($readOnly)
                @can('update', $task)
                    <button type="button" x-on:click="$dispatch('open-task-edit', { taskId: {{ $task->id }} })" title="Editar" aria-label="Editar" class="btn btn-sm btn-link text-body-secondary p-0 lh-1">
                        <x-icon name="pencil" />
                    </button>
                @endcan
            @endunless
        </div>

        <a href="{{ route('tasks.show', $task) }}" class="d-block fw-semibold text-body text-decoration-none mt-2 lh-sm">
            {{ $task->title }}
        </a>

        @if ($task->rejection_reason || $notApprovedByHomologation || $notDeployed || $task->isLate() || ($task->due_at && ! $task->completed_at))
            <div class="d-flex flex-wrap gap-1 mt-2">
                @if ($task->rejection_reason)
                    <x-badge color="danger">Reprovada</x-badge>
                @endif

                @if ($notApprovedByHomologation)
                    <x-badge color="warning">Não homologado</x-badge>
                @endif

                @if ($notDeployed)
                    <x-badge color="warning">Não implantado</x-badge>
                @endif

                @if ($task->isLate())
                    <x-badge color="danger"><x-icon name="alert" /> Atrasada</x-badge>
                @elseif ($task->due_at && ! $task->completed_at)
                    <x-badge color="secondary"><i class="bi bi-calendar-event" aria-hidden="true"></i> {{ $task->due_at->format('d/m H:i') }}</x-badge>
                @endif
            </div>
        @endif

        <div class="d-flex align-items-center justify-content-between gap-2 mt-3 pt-2 border-top">
            @if ($task->assignedTo)
                <span class="d-flex align-items-center gap-2 min-w-0">
                    <x-avatar :user="$task->assignedTo" size="sm" />
                    <span class="small text-body-secondary text-truncate">{{ $task->assignedTo->name }}</span>
                </span>
            @else
                <span class="small text-body-tertiary">Não atribuído</span>
            @endif

            <span class="d-flex align-items-center gap-2 flex-shrink-0">
                <span class="small text-body-secondary" title="Prioridade">{{ $task->priority->name }}</span>
                <x-xp-badge :amount="$task->xpValue()" />
            </span>
        </div>

        @unless ($readOnly)
            @can('claim', $task)
                <button type="button" wire:click="claim({{ $task->id }})" class="btn btn-sm btn-outline-primary w-100 mt-2">
                    <i class="bi bi-hand-index-thumb" aria-hidden="true"></i> Assumir tarefa
                </button>
            @endcan
        @endunless
    </div>
</div>
