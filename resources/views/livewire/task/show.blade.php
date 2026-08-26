<div>
    <a href="{{ route('boards.show', $task->board) }}" class="inline-flex items-center gap-1 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
        &larr; {{ $task->board->name }}
    </a>

    <div class="mt-2 flex items-start justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $task->title }}</h1>

            @if ($task->rejection_reason)
                <span class="mt-1 inline-block rounded bg-rose-100 px-2 py-0.5 text-xs font-medium text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">
                    Reprovada
                </span>
            @endif
        </div>

        @can('update', $task)
            <button type="button" wire:click="toggleEdit" title="Editar" aria-label="Editar" class="rounded-md border p-2 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                <x-icon name="pencil" class="h-4 w-4" />
            </button>
        @endcan
    </div>

    @if ($task->rejection_reason)
        <div class="mt-3 rounded-md border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700 dark:border-rose-800 dark:bg-rose-900/20 dark:text-rose-300">
            <strong>Motivo da reprovação:</strong> {{ $task->rejection_reason }}
        </div>
    @endif

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

    @if ($task->status === \App\Enums\TaskStatus::TESTING)
        <div class="mt-6 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
            <h2 class="mb-2 text-sm font-semibold text-gray-800 dark:text-gray-100">Revisão de teste</h2>

            <div class="flex flex-wrap gap-2">
                @can('approve', $task)
                    <button type="button" wire:click="approve" wire:confirm="Aprovar esta tarefa?" class="rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-emerald-500">
                        Aprovar
                    </button>
                @endcan

                @can('reject', $task)
                    <button type="button" wire:click="toggleRejectForm" class="rounded-md border border-rose-300 px-3 py-1.5 text-sm font-medium text-rose-600 hover:bg-rose-50 dark:border-rose-800 dark:text-rose-400 dark:hover:bg-rose-900/30">
                        Reprovar
                    </button>
                @endcan
            </div>

            @if ($showRejectForm)
                <form wire:submit="reject" class="mt-3 space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Motivo da reprovação (obrigatório)</label>
                    <textarea wire:model="rejectionReasonInput" rows="3" required
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
                    @error('rejectionReasonInput') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                    <div class="flex gap-2">
                        <button type="submit" class="rounded-md bg-rose-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-rose-500">
                            Confirmar reprovação
                        </button>
                        <button type="button" wire:click="toggleRejectForm" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                            Cancelar
                        </button>
                    </div>
                </form>
            @endif
        </div>
    @endif

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

    <div class="mt-6">
        <h2 class="mb-2 text-sm font-semibold text-gray-800 dark:text-gray-100">Movimentações</h2>

        <div class="space-y-2">
            @forelse ($task->movements as $movement)
                <div class="rounded-md border border-gray-100 px-3 py-2 text-sm dark:border-gray-700" wire:key="movement-{{ $movement->id }}">
                    <span class="text-gray-600 dark:text-gray-300">
                        {{ $movement->user->name }} moveu de
                        <strong>{{ $movement->fromColumn?->name ?? '—' }}</strong> para
                        <strong>{{ $movement->toColumn->name }}</strong>
                        &middot; {{ $movement->created_at->format('d/m/Y H:i') }}
                    </span>

                    @if ($movement->note)
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $movement->note }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-400">Nenhuma movimentação registrada ainda.</p>
            @endforelse
        </div>
    </div>

    @if ($showEditModal)
        <livewire:task.edit :task-id="$task->id" wire:key="task-edit-{{ $task->id }}" />
    @endif
</div>
