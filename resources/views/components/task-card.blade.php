@props(['task', 'readOnly' => false])

@php
    $notApprovedByHomologation = $task->status === \App\Enums\TaskStatus::APPROVED
        && ! $task->taskEvents->contains('type', \App\Enums\TaskEventType::HOMOLOGATION_COMPLETED);
    $notDeployed = $task->status === \App\Enums\TaskStatus::DONE
        && ! $task->taskEvents->contains('type', \App\Enums\TaskEventType::DEPLOYED);
    $priorityIcon = match (mb_strtolower($task->priority->name)) {
        'crítica', 'critica' => 'bi-chevron-double-up text-danger',
        'alta' => 'bi-chevron-up text-danger',
        'normal', 'média', 'media' => 'bi-list text-warning',
        'baixa' => 'bi-chevron-down text-primary',
        default => 'bi-dash-lg text-body-secondary',
    };
    $searchText = mb_strtolower($task->title.' dq-'.$task->id.' '.$task->category->name.' '.($task->assignedTo?->name ?? ''));
    $isMine = $task->assigned_to === auth()->id() ? '1' : '0';
@endphp

<div
    data-search="{{ $searchText }}"
    data-mine="{{ $isMine }}"
    x-show="(! search || $el.dataset.search.includes(search.toLowerCase())) && (! mine || $el.dataset.mine === '1')"
    @unless ($readOnly)
        draggable="true"
        x-on:dragstart="draggingTaskId = {{ $task->id }}"
        x-on:dragend="draggingTaskId = null; overColumnId = null"
        :class="{ 'kanban-card--dragging': draggingTaskId === {{ $task->id }} }"
    @endunless
    class="card kanban-card"
>
    <div class="card-body px-3 py-3">
        <div class="d-flex align-items-start justify-content-between gap-2">
            <a href="{{ route('tasks.show', $task) }}" class="text-body text-decoration-none lh-base">
                {{ $task->title }}
            </a>

            @unless ($readOnly)
                @can('update', $task)
                    <button type="button" x-on:click="$dispatch('open-task-edit', { taskId: {{ $task->id }} })" title="Editar" aria-label="Editar" class="btn btn-link text-body-tertiary p-0 lh-1 flex-shrink-0">
                        <x-icon name="pencil" />
                    </button>
                @endcan
            @endunless
        </div>

        <div class="d-flex flex-wrap align-items-center gap-1 mt-2">
            <span class="jira-tag" style="background-color: {{ $task->category->color }}; color: {{ $task->category->text_color }};">{{ $task->category->name }}</span>

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
                <x-badge><i class="bi bi-calendar-event" aria-hidden="true"></i> {{ $task->due_at->format('d/m H:i') }}</x-badge>
            @endif
        </div>

        <div class="d-flex align-items-center justify-content-between gap-2 mt-3">
            <span class="d-flex align-items-center gap-2">
                <i class="bi {{ $priorityIcon }} fs-5 lh-1" title="Prioridade: {{ $task->priority->name }}" aria-label="Prioridade {{ $task->priority->name }}"></i>
                <span class="count-pill" title="Pontuação da tarefa">{{ number_format($task->xpValue()) }} XP</span>
            </span>

            <span class="d-flex align-items-center gap-2">
                <span class="kanban-card-key">DQ-{{ $task->id }}</span>

                @if ($task->assignedTo)
                    <x-avatar :user="$task->assignedTo" size="sm" :title="$task->assignedTo->name" />
                @elseif (! $readOnly && auth()->user()->can('claim', $task))
                    <button type="button" wire:click="claim({{ $task->id }})" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.75rem;">
                        Assumir
                    </button>
                @else
                    <span class="avatar avatar-sm bg-body-secondary text-body-tertiary" title="Não atribuído"><i class="bi bi-person" aria-hidden="true"></i></span>
                @endif
            </span>
        </div>
    </div>
</div>
