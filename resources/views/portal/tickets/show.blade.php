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

    <x-card class="mb-4">
        <div class="small text-body-secondary mb-2">Aberto em {{ $ticket->created_at->format('d/m/Y H:i') }}</div>
        <p class="mb-0" style="white-space: pre-line;">{{ $ticket->description }}</p>
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
                </li>
            @empty
                <li class="list-group-item small text-body-secondary">Aguardando a primeira resposta da equipe.</li>
            @endforelse
        </ul>

        <form method="POST" action="{{ route('portal.tickets.reply', $ticket) }}" class="card-footer bg-body vstack gap-2">
            @csrf
            <label for="body" class="form-label fw-medium small mb-0">{{ $ticket->status === \App\Enums\TicketStatus::RESOLVED ? 'Ainda precisa de ajuda? Responda para reabrir' : 'Enviar mensagem' }}</label>
            <textarea id="body" name="body" rows="3" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
            @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="text-end"><button type="submit" class="btn btn-primary"><i class="bi bi-send" aria-hidden="true"></i> Enviar</button></div>
        </form>
    </div>
</x-layouts.portal>
