<div class="flex flex-1 min-h-0 flex-col" x-data="{ draggingTaskId: null }">
    <x-page-header title="Arquivo" :subtitle="$board->name" :back="route('boards.show', $board)" backLabel="Voltar ao quadro" />

    <div class="min-h-0 flex-1 space-y-8 overflow-y-auto pb-4">
        <section>
            <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-forest">
                <span class="h-2.5 w-2.5 rounded-full bg-forest"></span>
                Concluídas ({{ $completed->count() }})
            </h3>

            @if ($completed->isEmpty())
                <p class="text-sm text-ink-muted">Nenhuma tarefa concluída arquivada.</p>
            @else
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($completed as $task)
                        <x-task-card :task="$task" :read-only="true" wire:key="archived-completed-{{ $task->id }}" />
                    @endforeach
                </div>
            @endif
        </section>

        <section>
            <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-terracotta">
                <span class="h-2.5 w-2.5 rounded-full bg-terracotta"></span>
                Não concluídas ({{ $notCompleted->count() }})
            </h3>

            @if ($notCompleted->isEmpty())
                <p class="text-sm text-ink-muted">Nenhuma tarefa não concluída arquivada.</p>
            @else
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($notCompleted as $task)
                        <x-task-card :task="$task" :read-only="true" wire:key="archived-not-completed-{{ $task->id }}" />
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
