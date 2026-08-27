<div>
    <a href="{{ route('boards.show', $task->board) }}" class="inline-flex items-center gap-1 rounded-lg border border-line px-3 py-1.5 text-sm font-medium text-ink hover:bg-line/20">
        &larr; {{ $task->board->name }}
    </a>

    <div class="mt-2 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink">{{ $task->title }}</h1>

            @if ($task->rejection_reason)
                <x-badge color="terracotta">Reprovada</x-badge>
            @endif
        </div>

        @can('update', $task)
            <button type="button" wire:click="toggleEdit" title="Editar" aria-label="Editar" class="rounded-lg border border-line p-2 text-ink-muted hover:bg-line/20">
                <x-icon name="pencil" class="h-4 w-4" />
            </button>
        @endcan
    </div>

    @if ($task->rejection_reason)
        <div class="mt-3 rounded-xl border border-terracotta/30 bg-terracotta/10 p-3 text-sm text-terracotta">
            <strong>Motivo da reprovação:</strong> {{ $task->rejection_reason }}
        </div>
    @endif

    <p class="mt-2 text-ink-muted">{{ $task->description }}</p>

    <dl class="mt-6 grid grid-cols-2 gap-4 text-sm sm:grid-cols-4">
        <div>
            <dt class="text-ink-muted">Categoria</dt>
            <dd class="font-medium text-ink">{{ $task->category->name }}</dd>
        </div>
        <div>
            <dt class="text-ink-muted">Prioridade</dt>
            <dd class="font-medium text-ink">{{ $task->priority->name }}</dd>
        </div>
        <div>
            <dt class="text-ink-muted">Status</dt>
            <dd class="font-medium text-ink">{{ $task->status->label() }}</dd>
        </div>
        <div>
            <dt class="text-ink-muted">Pontos</dt>
            <dd class="font-medium text-ink"><x-xp-badge :amount="$task->xpValue()" /></dd>
        </div>
        <div>
            <dt class="text-ink-muted">Prazo</dt>
            <dd class="font-medium text-ink">
                {{ $task->due_at?->format('d/m/Y H:i') ?? '—' }}
                @if ($task->isLate())
                    <x-badge color="terracotta">Atrasada</x-badge>
                @endif
            </dd>
        </div>
    </dl>

    <div class="mt-6 rounded-xl border border-line bg-card p-4">
        <h2 class="mb-2 text-sm font-semibold text-ink">Responsável</h2>

        @if ($task->assignedTo)
            <div class="flex items-center gap-2">
                @if ($task->assignedTo->person?->foto_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($task->assignedTo->person->foto_path) }}" alt="" class="h-8 w-8 rounded-full object-cover">
                @else
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-semibold text-white">
                        {{ $task->assignedTo->initials() }}
                    </span>
                @endif
                <span>
                    <span class="block text-sm font-medium text-ink">{{ $task->assignedTo->name }}</span>
                    @if ($task->assignedTo->selectedTitle)
                        <span class="block text-xs text-gold">{{ $task->assignedTo->selectedTitle->name }}</span>
                    @endif
                </span>
            </div>
        @else
            <p class="text-sm text-ink-muted">Não atribuído</p>
        @endif

        @can('claim', $task)
            <button type="button" wire:click="claim" class="mt-2 rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-hover">
                Assumir esta tarefa
            </button>
        @endcan

        @if ($developers->isNotEmpty())
            <div class="mt-3 flex items-center gap-2">
                <div class="flex-1">
                    <x-user-picker :users="$developers" model="assignToUserId" placeholder="Selecionar desenvolvedor..." />
                </div>
                <button type="button" wire:click="assignTo" class="rounded-lg border border-line px-3 py-2 text-sm text-ink hover:bg-line/20">
                    Atribuir
                </button>
            </div>
        @endif
    </div>

    @if ($task->status === \App\Enums\TaskStatus::TESTING)
        <div class="mt-6 rounded-xl border border-line bg-card p-4">
            <h2 class="mb-2 text-sm font-semibold text-ink">Revisão de teste</h2>

            <div class="flex flex-wrap gap-2">
                @can('approve', $task)
                    <button type="button" wire:click="approve" wire:confirm="Aprovar esta tarefa?" class="rounded-lg bg-forest px-3 py-1.5 text-sm font-semibold text-white hover:opacity-90">
                        Aprovar
                    </button>
                @endcan

                @can('reject', $task)
                    <button type="button" wire:click="toggleRejectForm" class="rounded-lg border border-terracotta/30 px-3 py-1.5 text-sm font-medium text-terracotta hover:bg-terracotta/10">
                        Reprovar
                    </button>
                @endcan
            </div>

            @if ($showRejectForm)
                <form wire:submit="reject" class="mt-3 space-y-2">
                    <label class="block text-sm font-medium text-ink">Motivo da reprovação (obrigatório)</label>
                    <textarea wire:model="rejectionReasonInput" rows="3" required
                        class="block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-sm text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30"></textarea>
                    @error('rejectionReasonInput') <p class="text-sm text-terracotta">{{ $message }}</p> @enderror

                    <div class="flex gap-2">
                        <button type="submit" class="rounded-lg bg-terracotta px-3 py-1.5 text-sm font-semibold text-white hover:opacity-90">
                            Confirmar reprovação
                        </button>
                        <button type="button" wire:click="toggleRejectForm" class="rounded-lg border border-line px-3 py-1.5 text-sm font-medium text-ink hover:bg-line/20">
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
                <button type="button" wire:click="markHomologationCompleted" class="rounded-lg border border-line px-3 py-1.5 text-sm text-ink hover:bg-line/20">
                    Marcar homologação como concluída
                </button>
            @endunless
        @endcan

        @can('markDeployed', $task)
            @unless ($hasDeployed)
                <button type="button" wire:click="markDeployed" class="rounded-lg border border-line px-3 py-1.5 text-sm text-ink hover:bg-line/20">
                    Marcar como implantado
                </button>
            @endunless
        @endcan
    </div>

    <div class="mt-6">
        <h2 class="mb-2 text-sm font-semibold text-ink">Histórico</h2>

        <div class="space-y-2">
            @foreach ($task->taskEvents as $event)
                <div class="flex items-center justify-between rounded-lg border border-line/60 px-3 py-2 text-sm" wire:key="event-{{ $event->id }}">
                    <span class="text-ink-muted">
                        {{ $event->user->name }} &middot; {{ $event->type->label() }} &middot; {{ $event->occurred_at->format('d/m/Y H:i') }}
                    </span>

                    @if ($event->xpTransaction)
                        <x-xp-badge :amount="$event->xpTransaction->amount" />
                    @endif
                </div>
            @endforeach

            @if ($completionBonus)
                <div class="flex items-center justify-between rounded-lg border border-line/60 px-3 py-2 text-sm">
                    <span class="text-ink-muted">Tarefa concluída &middot; pontuação base concedida</span>
                    <x-xp-badge :amount="$completionBonus->amount" />
                </div>
            @endif

            @if ($task->taskEvents->isEmpty() && ! $completionBonus)
                <p class="text-sm text-ink-muted">Nenhuma atividade ainda.</p>
            @endif
        </div>
    </div>

    <div class="mt-6">
        <h2 class="mb-2 text-sm font-semibold text-ink">Movimentações</h2>

        <div class="space-y-2">
            @forelse ($task->movements as $movement)
                <div class="rounded-lg border border-line/60 px-3 py-2 text-sm" wire:key="movement-{{ $movement->id }}">
                    <span class="text-ink-muted">
                        {{ $movement->user->name }} moveu de
                        <strong>{{ $movement->fromColumn?->name ?? '—' }}</strong> para
                        <strong>{{ $movement->toColumn->name }}</strong>
                        &middot; {{ $movement->created_at->format('d/m/Y H:i') }}
                    </span>

                    @if ($movement->note)
                        <p class="mt-1 text-xs text-ink-muted">{{ $movement->note }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-ink-muted">Nenhuma movimentação registrada ainda.</p>
            @endforelse
        </div>
    </div>

    @if ($showEditModal)
        <livewire:task.edit :task-id="$task->id" wire:key="task-edit-{{ $task->id }}" />
    @endif
</div>
