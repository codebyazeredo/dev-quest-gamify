<x-layouts.app>
    <div x-data="ticketPage({{ $ticket->id }})">
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
                            </li>
                        @empty
                            <li class="list-group-item small text-body-secondary">Nenhuma mensagem ainda.</li>
                        @endforelse
                    </ul>

                    <form x-on:submit.prevent="reply" class="card-footer bg-body vstack gap-2">
                        <label for="reply-body" class="form-label fw-medium small mb-0">Responder ao cliente</label>
                        <textarea id="reply-body" x-model="replyBody" x-bind:class="{ 'is-invalid': firstError('body') }" class="form-control" rows="3" required></textarea>
                        <div class="invalid-feedback" x-text="firstError('body')"></div>
                        <div class="text-end"><x-button type="submit" x-bind:disabled="isBusy"><i class="bi bi-send" aria-hidden="true"></i> Enviar resposta</x-button></div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4 vstack gap-4">
                <x-card>
                    <h2 class="h6 text-body-secondary text-uppercase mb-2">Situação</h2>
                    <x-badge :color="$ticket->status->color()">{{ $ticket->status->label() }}</x-badge>
                    <div class="small text-body-secondary mt-2">Aberto em {{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                    <div class="small text-body-secondary">{{ $ticket->client->email }}</div>
                </x-card>

                <x-card>
                    <h2 class="h6 text-body-secondary text-uppercase mb-2">Tarefa vinculada</h2>

                    @if ($ticket->task)
                        <a href="{{ route('tasks.show', $ticket->task) }}" class="text-decoration-none"><i class="bi bi-kanban me-1" aria-hidden="true"></i> DQ-{{ $ticket->task->id }} · {{ $ticket->task->title }}</a>
                    @else
                        <p class="small text-body-secondary">Transforme este chamado em uma tarefa para a equipe de desenvolvimento.</p>

                        <form x-on:submit.prevent="convert" class="vstack gap-2">
                            <select x-model="task.column_id" x-bind:class="{ 'is-invalid': firstError('column_id') }" class="form-select form-select-sm" aria-label="Coluna" required>
                                <option value="">Quadro e coluna...</option>
                                @foreach ($boards as $board)
                                    <optgroup label="{{ $board->name }}">
                                        @foreach ($board->columns as $column)
                                            <option value="{{ $column->id }}">{{ $column->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" x-text="firstError('column_id')"></div>

                            <select x-model="task.category_id" x-bind:class="{ 'is-invalid': firstError('category_id') }" class="form-select form-select-sm" aria-label="Categoria" required>
                                <option value="">Categoria...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>

                            <select x-model="task.priority_id" x-bind:class="{ 'is-invalid': firstError('priority_id') }" class="form-select form-select-sm" aria-label="Prioridade" required>
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
