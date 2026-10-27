<x-layouts.portal :title="'Chamado #'.$ticket->id" :client="$client">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('portal.tickets.index') }}">Meus chamados</a></li>
            <li class="breadcrumb-item active" aria-current="page">#{{ $ticket->id }}</li>
        </ol>
    </nav>

    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
        <h1 class="h4 mb-0">{{ $ticket->title }}</h1>
        <x-badge :color="$ticket->status->color()">{{ $ticket->status->label() }}</x-badge>
    </div>

    <div class="row g-4">
        <div class="@if ($progress) col-lg-8 @else col-12 @endif vstack gap-4">
            <x-card>
                <div class="small text-body-secondary mb-2">Aberto em {{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                <p class="mb-0" style="white-space: pre-line;">{{ $ticket->description }}</p>
                <x-attachment-chips :attachments="$ticket->attachments" route="portal.attachments.show" />
            </x-card>

            <div class="card">
                <div class="card-header bg-body fw-semibold"><i class="bi bi-chat-dots me-1" aria-hidden="true"></i> Conversa</div>

                <ul class="list-group list-group-flush">
                    @forelse ($ticket->messages as $message)
                        <li class="list-group-item">
                            <div class="small mb-1">
                                <span class="fw-medium">{{ $message->isFromStaff() ? $message->user->name : 'Você' }}</span>
                                @if ($message->isFromStaff())<x-badge color="primary">Equipe</x-badge>@endif
                                <span class="text-body-secondary">&middot; {{ $message->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div style="white-space: pre-line;">{{ $message->body }}</div>
                            <x-attachment-chips :attachments="$message->attachments" route="portal.attachments.show" />
                        </li>
                    @empty
                        <li class="list-group-item small text-body-secondary">Aguardando a primeira resposta da equipe.</li>
                    @endforelse
                </ul>

                <form method="POST" action="{{ route('portal.tickets.reply', $ticket) }}" enctype="multipart/form-data" class="card-footer bg-body vstack gap-2">
                    @csrf
                    <label for="body" class="form-label fw-medium mb-0">{{ $ticket->status === \App\Enums\TicketStatus::RESOLVED ? 'Ainda precisa de ajuda? Responda para reabrir' : 'Enviar mensagem' }}</label>
                    <textarea id="body" name="body" rows="3" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                    @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror

                    <div>
                        <input type="file" name="files[]" multiple class="form-control @if ($errors->has('files') || $errors->has('files.*')) is-invalid @endif" aria-label="Anexos">
                        @foreach (collect($errors->get('files'))->merge(collect($errors->get('files.*'))->flatten())->unique() as $message)
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @endforeach
                    </div>

                    <div class="text-end"><button type="submit" class="btn btn-primary"><i class="bi bi-send" aria-hidden="true"></i> Enviar</button></div>
                </form>
            </div>
        </div>

        @if ($progress)
            <div class="col-lg-4">
                <x-card>
                    <h2 class="h6 text-body-secondary text-uppercase mb-3"><i class="bi bi-kanban me-1" aria-hidden="true"></i> Andamento da solicitação</h2>
                    <x-ticket-progress :progress="$progress" />
                </x-card>
            </div>
        @endif
    </div>
</x-layouts.portal>
