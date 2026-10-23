<div x-data="{ draggingTaskId: null }">
    <x-page-header title="Arquivo" :subtitle="$board->name" :back="route('boards.show', $board)" backLabel="Voltar ao quadro" />

    <section class="mb-5">
        <h2 class="h6 d-flex align-items-center gap-2 text-success-emphasis mb-3">
            <i class="bi bi-check-circle-fill text-success" aria-hidden="true"></i>
            Concluídas
            <x-badge color="success">{{ $completed->count() }}</x-badge>
        </h2>

        @if ($completed->isEmpty())
            <p class="small text-body-secondary">Nenhuma tarefa concluída arquivada.</p>
        @else
            <div class="row g-3">
                @foreach ($completed as $task)
                    <div class="col-12 col-md-6 col-xl-4">
                        <x-task-card :task="$task" :read-only="true" wire:key="archived-completed-{{ $task->id }}" />
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section>
        <h2 class="h6 d-flex align-items-center gap-2 text-danger-emphasis mb-3">
            <i class="bi bi-x-circle-fill text-danger" aria-hidden="true"></i>
            Não concluídas
            <x-badge color="danger">{{ $notCompleted->count() }}</x-badge>
        </h2>

        @if ($notCompleted->isEmpty())
            <p class="small text-body-secondary">Nenhuma tarefa não concluída arquivada.</p>
        @else
            <div class="row g-3">
                @foreach ($notCompleted as $task)
                    <div class="col-12 col-md-6 col-xl-4">
                        <x-task-card :task="$task" :read-only="true" wire:key="archived-not-completed-{{ $task->id }}" />
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
