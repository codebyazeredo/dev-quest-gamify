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

    $viewer = auth()->user();
    $canDrag = ! $readOnly && ($viewer->isAdmin()
        || $viewer->isProductOwner()
        || ($viewer->isDeveloper() && ($task->assigned_to === null || $task->assigned_to === $viewer->id || $task->status === \App\Enums\TaskStatus::REVIEW)));
@endphp

<div
    data-task-id="{{ $task->id }}"
    data-search="{{ $searchText }}"
    data-category="{{ $task->category_id }}"
    data-priority="{{ $task->priority_id }}"
    data-mine="{{ $isMine }}"
    data-assignee="{{ $task->assigned_to ?? 'none' }}"
    data-late="{{ $task->isOverdue() ? '1' : '0' }}"
    x-show="matches($el)"
    @if ($canDrag)
        draggable="true"
        x-on:dragstart="startDrag($event, {{ $task->id }})"
        :class="{ 'kanban-card--dragging': draggingTaskId === {{ $task->id }} }"
    @endif
    class="card kanban-card {{ $isMine === '1' ? 'kanban-card--mine' : '' }}"
>
    <div class="card-body px-3 py-3">
        <div class="d-flex align-items-start justify-content-between gap-2">
            <a href="{{ route('tasks.show', $task) }}" draggable="false" class="text-body text-decoration-none lh-base">
                {{ $task->title }}
            </a>

            @unless ($readOnly)
                @can('update', $task)
                    <button type="button" x-on:click="$dispatch('open-task-form', { mode: 'edit', taskId: {{ $task->id }} })" title="Editar" aria-label="Editar" class="btn btn-ghost btn-icon btn-icon-sm flex-shrink-0">
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

            @if ($task->isLate() || $task->isOverdue())
                <x-badge color="danger"><x-icon name="alert" /> Atrasada</x-badge>
            @elseif ($task->due_at && ! $task->completed_at)
                <x-badge><i class="bi bi-calendar-event" aria-hidden="true"></i> {{ $task->due_at->format('d/m H:i') }}</x-badge>
            @endif
        </div>

        @if ($task->branch_name || $task->pull_request_url)
            <div class="d-flex flex-wrap align-items-center gap-2 mt-2 small">
                @if ($task->branch_name)
                    <button type="button" draggable="false" data-copy="{{ $task->branch_name }}" data-copy-label="Branch copiada" class="btn btn-link btn-sm p-0 text-body-secondary text-decoration-none text-truncate" style="max-width: 11rem;" title="Copiar branch: {{ $task->branch_name }}"><i class="bi bi-git" aria-hidden="true"></i> {{ $task->branch_name }}</button>
                @endif

                @if ($task->pull_request_url)
                    <span class="d-inline-flex align-items-center gap-1">
                        <a href="{{ $task->pull_request_url }}" target="_blank" rel="noopener noreferrer" draggable="false" class="text-decoration-none" title="Abrir pull request"><i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> PR</a>
                        <button type="button" draggable="false" data-copy="{{ $task->pull_request_url }}" data-copy-label="Link copiado" class="btn btn-link btn-sm p-0 text-body-secondary" title="Copiar link do pull request" aria-label="Copiar link do pull request"><i class="bi bi-clipboard" aria-hidden="true"></i></button>
                    </span>
                @endif
            </div>
        @endif

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
                    <button type="button" x-on:click="act('post', '/api/tasks/{{ $task->id }}/claim')" class="btn btn-ghost btn-ghost-primary btn-icon btn-icon-sm" data-bs-toggle="tooltip" data-bs-title="Assumir tarefa" aria-label="Assumir tarefa">
                        <i class="bi bi-hand-index-thumb" aria-hidden="true"></i>
                    </button>
                @else
                    <span class="avatar avatar-sm bg-body-secondary text-body-tertiary" title="Não atribuído"><i class="bi bi-person" aria-hidden="true"></i></span>
                @endif
            </span>
        </div>
    </div>
</div>
