<x-layouts.app>
    <div x-data="taskPage({{ $task->id }})">
        <x-page-header :title="$task->title" :back="route('boards.show', $task->board)" :backLabel="$task->board->name">
            @if ($task->rejection_reason || $task->isArchived())
                <x-slot:subtitle>
                    <div class="d-flex flex-wrap gap-2">
                        @if ($task->rejection_reason)
                            <x-badge color="danger">Reprovada</x-badge>
                        @endif

                        @if ($task->isArchived())
                            <x-badge color="secondary">Arquivada</x-badge>
                        @endif
                    </div>
                </x-slot:subtitle>
            @endif

            @can('unarchive', $task)
                @if ($task->isArchived())
                    <x-icon-button x-on:click="perform('unarchive')" icon="box-arrow-up" label="Desarquivar" />
                @endif
            @endcan

            @can('archive', $task)
                @unless ($task->isArchived())
                    <x-icon-button x-on:click="archive" icon="archive" label="Arquivar" />
                @endunless
            @endcan

            @can('update', $task)
                <x-icon-button x-on:click="$dispatch('open-task-form', { mode: 'edit', taskId: {{ $task->id }} })" icon="pencil-square" label="Editar tarefa" />
            @endcan
        </x-page-header>

        @if ($task->rejection_reason)
            <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                <div><strong>Motivo da reprovação:</strong> {{ $task->rejection_reason }}</div>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8 vstack gap-4">
                <x-card>
                    <h2 class="h6 text-body-secondary text-uppercase mb-2">Descrição</h2>
                    <p class="mb-0" style="white-space: pre-line;">{{ $task->description ?: 'Sem descrição.' }}</p>
                </x-card>

                @if ($task->branch_name || $task->pull_request_url)
                    <x-card>
                        <h2 class="h6 text-body-secondary text-uppercase mb-2">Git</h2>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            @if ($task->branch_name)
                                <span class="d-inline-flex align-items-center gap-1">
                                    <code><i class="bi bi-git" aria-hidden="true"></i> {{ $task->branch_name }}</code>
                                    <x-icon-button icon="clipboard" label="Copiar branch" data-copy="{{ $task->branch_name }}" data-copy-label="Branch copiada" class="btn-icon-sm" />
                                </span>
                            @endif

                            @if ($task->pull_request_url)
                                <span class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ $task->pull_request_url }}" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Abrir pull request</a>
                                    <x-icon-button icon="clipboard" label="Copiar link do pull request" data-copy="{{ $task->pull_request_url }}" data-copy-label="Link copiado" class="btn-icon-sm" />
                                </span>
                            @endif
                        </div>
                    </x-card>
                @endif

                @include('tasks.partials.attachments')

                @if ($task->status === \App\Enums\TaskStatus::TESTING)
                    <x-card variant="highlight">
                        <h2 class="h6 mb-3"><i class="bi bi-clipboard2-check me-1" aria-hidden="true"></i> Revisão de teste</h2>

                        <div class="d-flex flex-wrap gap-2">
                            @can('approve', $task)
                                <x-button variant="success" x-on:click="approve">
                                    <x-icon name="check" /> Aprovar
                                </x-button>
                            @endcan

                            @can('reject', $task)
                                <button type="button" x-on:click="toggleRejectForm" class="btn btn-outline-danger">
                                    <x-icon name="close" /> Reprovar
                                </button>
                            @endcan
                        </div>

                        <form x-show="showRejectForm" x-cloak x-on:submit.prevent="reject" class="vstack gap-2 mt-3">
                            <div>
                                <label for="rejection-reason" class="form-label fw-medium small mb-1">Motivo da reprovação (obrigatório)</label>
                                <textarea id="rejection-reason" x-model="rejectionReason" x-bind:class="{ 'is-invalid': firstError('reason') }" class="form-control" required></textarea>
                                <div class="invalid-feedback" x-text="firstError('reason')"></div>
                            </div>

                            <div class="d-flex gap-2">
                                <x-button type="submit" variant="danger">Confirmar reprovação</x-button>
                                <x-button variant="secondary" x-on:click="toggleRejectForm">Cancelar</x-button>
                            </div>
                        </form>
                    </x-card>
                @endif

                @if ((auth()->user()->can('markHomologationCompleted', $task) && ! $hasHomologation) || (auth()->user()->can('markDeployed', $task) && ! $hasDeployed))
                    <div class="d-flex flex-wrap gap-2">
                        @can('markHomologationCompleted', $task)
                            @unless ($hasHomologation)
                                <button type="button" x-on:click="perform('homologation')" class="btn btn-outline-primary">
                                    <i class="bi bi-patch-check" aria-hidden="true"></i> Marcar homologação como concluída
                                </button>
                            @endunless
                        @endcan

                        @can('markDeployed', $task)
                            @unless ($hasDeployed)
                                <button type="button" x-on:click="perform('deploy')" class="btn btn-outline-primary">
                                    <i class="bi bi-cloud-arrow-up" aria-hidden="true"></i> Marcar como implantado
                                </button>
                            @endunless
                        @endcan
                    </div>
                @endif

                <div class="card">
                    <div class="card-header bg-body fw-semibold"><i class="bi bi-clock-history me-1" aria-hidden="true"></i> Histórico</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($task->taskEvents as $event)
                            <li class="list-group-item d-flex align-items-center justify-content-between gap-3">
                                <span class="small">
                                    <span class="fw-medium">{{ $event->user->name }}</span>
                                    <span class="text-body-secondary">&middot; {{ $event->type->label() }} &middot; {{ $event->occurred_at->format('d/m/Y H:i') }}</span>
                                </span>

                                @if ($event->xpTransaction)
                                    <x-xp-badge :amount="$event->xpTransaction->amount" />
                                @endif
                            </li>
                        @endforeach

                        @if ($completionBonus)
                            <li class="list-group-item d-flex align-items-center justify-content-between gap-3">
                                <span class="small text-body-secondary">Tarefa concluída &middot; pontuação base concedida</span>
                                <x-xp-badge :amount="$completionBonus->amount" />
                            </li>
                        @endif

                        @if ($task->taskEvents->isEmpty() && ! $completionBonus)
                            <li class="list-group-item small text-body-secondary">Nenhuma atividade ainda.</li>
                        @endif
                    </ul>
                </div>

                <div class="card">
                    <div class="card-header bg-body fw-semibold"><i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i> Movimentações</div>
                    <ul class="list-group list-group-flush">
                        @forelse ($task->movements as $movement)
                            <li class="list-group-item">
                                <span class="small">
                                    <span class="fw-medium">{{ $movement->user->name }}</span>
                                    <span class="text-body-secondary">moveu de</span>
                                    <x-badge>{{ $movement->fromColumn?->name ?? '—' }}</x-badge>
                                    <span class="text-body-secondary">para</span>
                                    <x-badge color="primary">{{ $movement->toColumn->name }}</x-badge>
                                    <span class="text-body-secondary">&middot; {{ $movement->created_at->format('d/m/Y H:i') }}</span>
                                </span>

                                @if ($movement->note)
                                    <p class="small text-body-secondary mt-1 mb-0">{{ $movement->note }}</p>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item small text-body-secondary">Nenhuma movimentação registrada ainda.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 vstack gap-4">
                <div class="card">
                    <div class="card-header bg-body fw-semibold">Detalhes</div>
                    <dl class="list-group list-group-flush mb-0">
                        <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                            <dt class="small text-body-secondary fw-normal">Categoria</dt>
                            <dd class="mb-0">
                                <span class="badge rounded-pill fw-medium" style="background-color: {{ $task->category->color }}; color: {{ $task->category->text_color }};">{{ $task->category->name }}</span>
                            </dd>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                            <dt class="small text-body-secondary fw-normal">Prioridade</dt>
                            <dd class="mb-0 fw-medium">{{ $task->priority->name }}</dd>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                            <dt class="small text-body-secondary fw-normal">Status</dt>
                            <dd class="mb-0 fw-medium">{{ $task->status?->label() ?? 'Sem marco' }}</dd>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                            <dt class="small text-body-secondary fw-normal">Pontos</dt>
                            <dd class="mb-0"><x-xp-badge :amount="$task->xpValue()" /></dd>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                            <dt class="small text-body-secondary fw-normal">Prazo</dt>
                            <dd class="mb-0 fw-medium text-end">
                                {{ $task->due_at?->format('d/m/Y H:i') ?? '—' }}
                                @if ($task->isLate())
                                    <x-badge color="danger">Atrasada</x-badge>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <x-card>
                    <h2 class="h6 text-body-secondary text-uppercase mb-3">Responsável</h2>

                    @if ($task->assignedTo)
                        <div class="d-flex align-items-center gap-3">
                            <x-avatar :user="$task->assignedTo" size="lg" />
                            <div class="min-w-0">
                                <div class="fw-semibold text-truncate">{{ $task->assignedTo->name }}</div>
                                @if ($task->assignedTo->selectedTitle)
                                    <div class="small text-warning-emphasis">{{ $task->assignedTo->selectedTitle->name }}</div>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="small text-body-secondary mb-0">Não atribuído</p>
                    @endif

                    @can('claim', $task)
                        <button type="button" x-on:click="perform('claim')" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-hand-index-thumb" aria-hidden="true"></i> Assumir esta tarefa
                        </button>
                    @endcan

                    @if ($developers->isNotEmpty())
                        <div class="vstack gap-2 mt-3 pt-3 border-top">
                            <x-user-picker :users="$developers" x-model="assignToUserId" placeholder="Selecionar desenvolvedor..." />
                            <button type="button" x-on:click="assign" class="btn btn-outline-secondary">Atribuir</button>
                        </div>
                    @endif
                </x-card>
            </div>
        </div>

        @can('update', $task)
            @include('tasks.partials.task-modal', ['assignableUsers' => collect()])
        @endcan
    </div>
</x-layouts.app>
