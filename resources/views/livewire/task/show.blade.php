<div>
    <a href="{{ route('boards.show', $task->board) }}" class="text-sm text-indigo-600 hover:underline">&larr; {{ $task->board->name }}</a>

    <div class="mt-2 flex items-start justify-between">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $task->title }}</h1>

        @can('update', $task)
            <button type="button" wire:click="toggleEdit" class="rounded-md border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                Editar
            </button>
        @endcan
    </div>

    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $task->description }}</p>

    <dl class="mt-6 grid grid-cols-2 gap-4 text-sm sm:grid-cols-4">
        <div>
            <dt class="text-gray-500 dark:text-gray-400">Categoria</dt>
            <dd class="font-medium text-gray-800 dark:text-gray-100">{{ $task->category->name }}</dd>
        </div>
        <div>
            <dt class="text-gray-500 dark:text-gray-400">Prioridade</dt>
            <dd class="font-medium text-gray-800 dark:text-gray-100">{{ $task->priority->label() }}</dd>
        </div>
        <div>
            <dt class="text-gray-500 dark:text-gray-400">Status</dt>
            <dd class="font-medium text-gray-800 dark:text-gray-100">{{ $task->status->label() }}</dd>
        </div>
        <div>
            <dt class="text-gray-500 dark:text-gray-400">Pontos</dt>
            <dd class="font-medium text-gray-800 dark:text-gray-100"><x-xp-badge :amount="$task->xpValue()" /></dd>
        </div>
        <div>
            <dt class="text-gray-500 dark:text-gray-400">Prazo</dt>
            <dd class="font-medium text-gray-800 dark:text-gray-100">
                {{ $task->due_at?->format('d/m/Y H:i') ?? '—' }}
                @if ($task->isLate())
                    <span class="ml-1 rounded bg-rose-100 px-1.5 py-0.5 text-xs font-medium text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">Atrasada</span>
                @endif
            </dd>
        </div>
    </dl>

    <div class="mt-6 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
        <h2 class="mb-2 text-sm font-semibold text-gray-800 dark:text-gray-100">Responsável</h2>

        @if ($task->assignedTo)
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $task->assignedTo->name }}</p>
        @else
            <p class="text-sm text-gray-400">Não atribuído</p>
        @endif

        @can('claim', $task)
            <button type="button" wire:click="claim" class="mt-2 rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-500">
                Assumir esta tarefa
            </button>
        @endcan

        @if ($developers->isNotEmpty())
            <div class="mt-3 flex items-center gap-2">
                <select wire:model="assignToUserId" class="rounded-md border border-gray-300 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">Selecionar desenvolvedor...</option>
                    @foreach ($developers as $developer)
                        <option value="{{ $developer->id }}">{{ $developer->name }}</option>
                    @endforeach
                </select>
                <button type="button" wire:click="assignTo" class="rounded-md border px-3 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    Atribuir
                </button>
            </div>
        @endif
    </div>

    <div class="mt-6 flex gap-2">
        @can('markHomologationCompleted', $task)
            @unless ($hasHomologation)
                <button type="button" wire:click="markHomologationCompleted" class="rounded-md border px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    Marcar homologação como concluída
                </button>
            @endunless
        @endcan

        @can('markDeployed', $task)
            @unless ($hasDeployed)
                <button type="button" wire:click="markDeployed" class="rounded-md border px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    Marcar como implantado
                </button>
            @endunless
        @endcan
    </div>

    <div class="mt-6">
        <h2 class="mb-2 text-sm font-semibold text-gray-800 dark:text-gray-100">Histórico</h2>

        <div class="space-y-2">
            @foreach ($task->taskEvents as $event)
                <div class="flex items-center justify-between rounded-md border border-gray-100 px-3 py-2 text-sm dark:border-gray-700" wire:key="event-{{ $event->id }}">
                    <span class="text-gray-600 dark:text-gray-300">
                        {{ $event->user->name }} &middot; {{ $event->type->label() }} &middot; {{ $event->occurred_at->format('d/m/Y H:i') }}
                    </span>

                    @if ($event->xpTransaction)
                        <x-xp-badge :amount="$event->xpTransaction->amount" />
                    @endif
                </div>
            @endforeach

            @if ($completionBonus)
                <div class="flex items-center justify-between rounded-md border border-gray-100 px-3 py-2 text-sm dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-300">Tarefa concluída &middot; pontuação base concedida</span>
                    <x-xp-badge :amount="$completionBonus->amount" />
                </div>
            @endif

            @if ($task->taskEvents->isEmpty() && ! $completionBonus)
                <p class="text-sm text-gray-400">Nenhuma atividade ainda.</p>
            @endif
        </div>
    </div>

    @if ($showEditModal)
        <livewire:task.edit :task-id="$task->id" wire:key="task-edit-{{ $task->id }}" />
    @endif
</div>
