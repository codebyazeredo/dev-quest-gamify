<x-layouts.app>
    <div x-data="ticketPage({{ $ticket->id }}, @js($boards), @js($ticket->title))">
        <x-page-header :title="'Chamado #'.$ticket->id.' · '.$ticket->title" :subtitle="$ticket->client->razao_social" :back="route('tickets.index')" backLabel="Chamados" />

        <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
            <select x-on:change="changeStatus($event.target.value)" class="form-select form-select-sm w-auto" aria-label="Situação do chamado">
                @foreach (\App\Enums\TicketStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($ticket->status === $status)>{{ $status->label() }}</option>
                @endforeach
            </select>

            <select x-on:change="changePriority($event.target.value)" class="form-select form-select-sm w-auto" aria-label="Prioridade do chamado">
                @foreach (\App\Enums\TicketPriority::cases() as $priority)
                    <option value="{{ $priority->value }}" @selected($ticket->priority === $priority)>Prioridade: {{ $priority->label() }}</option>
                @endforeach
            </select>
            <select x-on:change="changeAssignee($event.target.value)" class="form-select form-select-sm w-auto" aria-label="Responsável pelo chamado">
                <option value="">Sem responsável</option>
                @foreach ($staff as $member)
                    <option value="{{ $member->id }}" @selected($ticket->assigned_to === $member->id)>{{ $member->name }}</option>
                @endforeach
            </select>

            @if ($ticket->assigned_to !== auth()->id())
                <x-button type="button" class="btn-sm" x-on:click="claim"><i class="bi bi-hand-index-thumb" aria-hidden="true"></i> Assumir</x-button>
            @endif
        </div>

        <div class="row g-4">
            <div class="col-lg-8 vstack gap-4">
                <x-card>
                    <h2 class="h6 fw-semibold mb-2">Descrição</h2>
                    <p class="mb-0" style="white-space: pre-line;">{{ $ticket->description }}</p>
                    <x-attachment-chips :attachments="$ticket->attachments" route="ticket-attachments.show" />
                </x-card>

                <div class="card">
                    <div class="card-header bg-body fw-semibold"><i class="bi bi-chat-dots me-1" aria-hidden="true"></i> Conversa</div>
                    <ul class="list-group list-group-flush">
                        @forelse ($ticket->messages as $message)
                            <li class="list-group-item {{ $message->is_internal ? 'bg-warning-subtle' : '' }}">
                                <div class="small mb-1">
                                    <span class="fw-medium">{{ $message->isFromStaff() ? $message->user->name : $ticket->client->razao_social }}</span>
                                    @if ($message->is_internal)
                                        <x-badge color="warning"><i class="bi bi-eye-slash" aria-hidden="true"></i> Nota interna</x-badge>
                                    @else
                                        <x-badge :color="$message->isFromStaff() ? 'primary' : 'secondary'">{{ $message->isFromStaff() ? 'Equipe' : 'Cliente' }}</x-badge>
                                    @endif
                                    <span class="text-body-secondary">&middot; {{ $message->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div style="white-space: pre-line;">{{ $message->body }}</div>
                                <x-attachment-chips :attachments="$message->attachments" route="ticket-attachments.show" />
                            </li>
                        @empty
                            <li class="list-group-item small text-body-secondary">Nenhuma mensagem ainda.</li>
                        @endforelse
                    </ul>

                    <form x-on:submit.prevent="reply" class="card-footer vstack gap-2" x-bind:class="replyMode === 'internal' ? 'bg-warning-subtle' : 'bg-body'">
                        <div class="btn-group btn-group-sm align-self-start" role="group" aria-label="Tipo de mensagem">
                            <button type="button" class="btn" x-bind:class="replyMode === 'public' ? 'btn-primary' : 'btn-outline-secondary'" x-on:click="replyMode = 'public'"><i class="bi bi-send" aria-hidden="true"></i> Responder ao cliente</button>
                            <button type="button" class="btn" x-bind:class="replyMode === 'internal' ? 'btn-warning' : 'btn-outline-secondary'" x-on:click="replyMode = 'internal'"><i class="bi bi-eye-slash" aria-hidden="true"></i> Nota interna</button>
                        </div>
                        <p class="small text-body-secondary mb-0" x-show="replyMode === 'internal'" x-cloak>Visível só para a equipe de suporte. O cliente não recebe e-mail nem vê esta nota.</p>

                        <label for="reply-body" class="visually-hidden">Mensagem</label>
                        <textarea id="reply-body" x-model="replyBody" x-bind:class="{ 'is-invalid': firstError('body') }" class="form-control" rows="3" required></textarea>
                        <div class="invalid-feedback" x-text="firstError('body')"></div>

                        <div>
                            <x-file-input x-ref="replyFiles" aria-label="Anexos" />
                            <div class="text-danger small mt-1" x-show="firstError('files') || firstError('files.0')" x-cloak x-text="firstError('files') || firstError('files.0')"></div>
                        </div>

                        <div class="text-end"><x-button type="submit" x-bind:disabled="isBusy"><i class="bi" x-bind:class="replyMode === 'internal' ? 'bi-eye-slash' : 'bi-send'" aria-hidden="true"></i> <span x-text="replyMode === 'internal' ? 'Salvar nota interna' : 'Enviar resposta'"></span></x-button></div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4 vstack gap-4">
                <x-card>
                    <h2 class="h6 fw-semibold mb-2">Cliente</h2>
                    <div class="fw-semibold">{{ $ticket->client->razao_social }}</div>
                    <div class="small text-body-secondary">{{ $ticket->client->formatted_cnpj }}</div>
                    @if ($ticket->client->businessSector)
                        <div class="small mt-2"><i class="bi bi-briefcase me-1" aria-hidden="true"></i> {{ $ticket->client->businessSector->name }}</div>
                    @endif
                    <div class="small mt-1"><i class="bi bi-envelope me-1" aria-hidden="true"></i> {{ $ticket->client->email }}</div>
                    @if ($ticket->client->address)
                        <div class="small mt-1"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i> {{ $ticket->client->address->formatted }}</div>
                    @endif
                </x-card>

                <x-card>
                    <h2 class="h6 fw-semibold mb-2">Situação</h2>
                    <x-badge :color="$ticket->status->color()">{{ $ticket->status->label() }}</x-badge>
                    <x-badge :color="$ticket->priority->color()">{{ $ticket->priority->label() }}</x-badge>
                    <div class="small mt-2"><i class="bi bi-person me-1" aria-hidden="true"></i> {{ $ticket->assignedTo?->name ?? 'Sem responsável' }}</div>
                    <div class="small text-body-secondary mt-1">Aberto em {{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                    <div class="small text-body-secondary mt-1">
                        1ª resposta: {{ $ticket->first_responded_at ? $ticket->first_responded_at->format('d/m/Y H:i').' ('.$ticket->created_at->locale('pt_BR')->diffForHumans($ticket->first_responded_at, ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]).')' : 'ainda sem resposta' }}
                    </div>
                    @if ($ticket->due_at)
                        <div class="small mt-1 {{ $ticket->isOverdue() ? 'text-danger fw-medium' : 'text-body-secondary' }}">
                            Prazo de resposta: {{ $ticket->due_at->format('d/m/Y H:i') }}
                            @if ($ticket->isOverdue())
                                <x-badge color="danger">Vencido</x-badge>
                            @endif
                        </div>
                    @endif
                </x-card>

                @if ($otherTickets->isNotEmpty())
                    <x-card>
                        <h2 class="h6 fw-semibold mb-2">Outros chamados do cliente</h2>
                        <ul class="list-unstyled mb-0 vstack gap-2">
                            @foreach ($otherTickets as $other)
                                <li class="d-flex align-items-center justify-content-between gap-2">
                                    <a href="{{ route('tickets.show', $other) }}" class="text-body text-decoration-none text-truncate">#{{ $other->id }} · {{ $other->title }}</a>
                                    <x-badge :color="$other->status->color()">{{ $other->status->label() }}</x-badge>
                                </li>
                            @endforeach
                        </ul>
                    </x-card>
                @endif

                <x-card>
                    <h2 class="h6 fw-semibold mb-3">Tarefa vinculada</h2>

                    @if ($ticket->task)
                        <a href="{{ route('tasks.show', $ticket->task) }}" class="d-block text-decoration-none mb-3"><i class="bi bi-kanban me-1" aria-hidden="true"></i> DQ-{{ $ticket->task->id }} · {{ $ticket->task->title }}</a>

                        @if ($progress)
                            <x-ticket-progress :progress="$progress" />
                        @endif
                    @else
                        <p class="small text-body-secondary">Responda ao cliente quando precisar e, ao decidir, transforme este chamado em uma tarefa.</p>

                        <form x-on:submit.prevent="convert" class="vstack gap-2">
                            <select x-model="task.board_id" x-on:change="task.column_id = ''" x-bind:class="{ 'is-invalid': firstError('board_id') }" class="form-select" aria-label="Quadro" required>
                                <option value="">Quadro...</option>
                                <template x-for="board in boards" :key="board.id">
                                    <option x-bind:value="board.id" x-text="board.name"></option>
                                </template>
                            </select>
                            <div class="invalid-feedback" x-text="firstError('board_id')"></div>

                            <select x-model="task.column_id" x-bind:disabled="! task.board_id" x-bind:class="{ 'is-invalid': firstError('column_id') }" class="form-select" aria-label="Coluna" required>
                                <option value="">Coluna...</option>
                                <template x-for="column in columns()" :key="column.id">
                                    <option x-bind:value="column.id" x-text="column.name"></option>
                                </template>
                            </select>
                            <div class="invalid-feedback" x-text="firstError('column_id')"></div>

                            <input type="text" x-model="task.title" x-bind:class="{ 'is-invalid': firstError('title') }" class="form-control" placeholder="Título da tarefa" aria-label="Título da tarefa" required>
                            <div class="invalid-feedback" x-text="firstError('title')"></div>

                            <select x-model="task.category_id" x-bind:class="{ 'is-invalid': firstError('category_id') }" class="form-select" aria-label="Categoria" required>
                                <option value="">Categoria...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>

                            <select x-model="task.priority_id" x-bind:class="{ 'is-invalid': firstError('priority_id') }" class="form-select" aria-label="Prioridade" required>
                                <option value="">Prioridade...</option>
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                @endforeach
                            </select>

                            <x-button type="submit" x-bind:disabled="isBusy"><i class="bi bi-kanban" aria-hidden="true"></i> Gerar tarefa</x-button>
                        </form>
                    @endif
                </x-card>
            </div>
        </div>
    </div>
</x-layouts.app>
