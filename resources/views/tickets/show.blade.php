<x-layouts.app>
    <div x-data="ticketPage({{ $ticket->id }}, @js($boards), @js($ticket->title))">
        <x-page-header :title="'Chamado #'.$ticket->id.' · '.$ticket->title" :subtitle="$ticket->client->razao_social" :back="route('tickets.index')" backLabel="Chamados">
            <select x-on:change="changeStatus($event.target.value)" class="form-select form-select-sm w-auto" aria-label="Situação do chamado">
                @foreach (\App\Enums\TicketStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($ticket->status === $status)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </x-page-header>

        <div class="row g-4">
            <div class="col-lg-8 vstack gap-4">
                <x-card>
                    <h2 class="h6 text-body-secondary text-uppercase mb-2">Descrição</h2>
                    <p class="mb-0" style="white-space: pre-line;">{{ $ticket->description }}</p>
                    <x-attachment-chips :attachments="$ticket->attachments" route="ticket-attachments.show" />
                </x-card>

                <div class="card">
                    <div class="card-header bg-body fw-semibold"><i class="bi bi-chat-dots me-1" aria-hidden="true"></i> Conversa</div>
                    <ul class="list-group list-group-flush">
                        @forelse ($ticket->messages as $message)
                            <li class="list-group-item">
                                <div class="small mb-1">
                                    <span class="fw-medium">{{ $message->isFromStaff() ? $message->user->name : $ticket->client->razao_social }}</span>
                                    <x-badge :color="$message->isFromStaff() ? 'primary' : 'secondary'">{{ $message->isFromStaff() ? 'Equipe' : 'Cliente' }}</x-badge>
                                    <span class="text-body-secondary">&middot; {{ $message->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div style="white-space: pre-line;">{{ $message->body }}</div>
                                <x-attachment-chips :attachments="$message->attachments" route="ticket-attachments.show" />
                            </li>
                        @empty
                            <li class="list-group-item small text-body-secondary">Nenhuma mensagem ainda.</li>
                        @endforelse
                    </ul>

                    <form x-on:submit.prevent="reply" class="card-footer bg-body vstack gap-2">
                        <label for="reply-body" class="form-label fw-medium mb-0">Responder ao cliente</label>
                        <textarea id="reply-body" x-model="replyBody" x-bind:class="{ 'is-invalid': firstError('body') }" class="form-control" rows="3" required></textarea>
                        <div class="invalid-feedback" x-text="firstError('body')"></div>

                        <div>
                            <input type="file" multiple x-on:change="pickReplyFiles" x-bind:class="{ 'is-invalid': firstError('files') || firstError('files.0') }" class="form-control" aria-label="Anexos">
                            <div class="invalid-feedback" x-text="firstError('files') || firstError('files.0')"></div>
                        </div>

                        <div class="text-end"><x-button type="submit" x-bind:disabled="isBusy"><i class="bi bi-send" aria-hidden="true"></i> Enviar resposta</x-button></div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4 vstack gap-4">
                <x-card>
                    <h2 class="h6 text-body-secondary text-uppercase mb-2">Cliente</h2>
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
                    <h2 class="h6 text-body-secondary text-uppercase mb-2">Situação</h2>
                    <x-badge :color="$ticket->status->color()">{{ $ticket->status->label() }}</x-badge>
                    <div class="small text-body-secondary mt-2">Aberto em {{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                </x-card>

                <x-card>
                    <h2 class="h6 text-body-secondary text-uppercase mb-3">Tarefa vinculada</h2>

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
