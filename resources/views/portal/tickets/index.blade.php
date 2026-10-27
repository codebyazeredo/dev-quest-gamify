<x-layouts.portal title="Meus chamados" :client="$client">
    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h4 mb-0">Meus chamados</h1>
            <p class="small text-body-secondary mb-0">Chamados abertos por {{ $client->razao_social }}.</p>
        </div>
    </div>

    <x-card class="mb-4">
        <h2 class="h6 mb-3">Abrir novo chamado</h2>

        <form method="POST" action="{{ route('portal.tickets.store') }}" enctype="multipart/form-data" class="vstack gap-3">
            @csrf

            <div>
                <label for="title" class="form-label fw-medium">Assunto</label>
                <input id="title" type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="description" class="form-label fw-medium">Descreva o problema ou a solicitação</label>
                <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="files" class="form-label fw-medium">Anexos <span class="text-body-secondary fw-normal">(opcional)</span></label>
                <input id="files" type="file" name="files[]" multiple class="form-control @if ($errors->has('files') || $errors->has('files.*')) is-invalid @endif">
                @foreach (collect($errors->get('files'))->merge(collect($errors->get('files.*'))->flatten())->unique() as $message)
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @endforeach
                <div class="form-text">Imagens, PDF, documentos, planilhas, texto e ZIP. Até 10 arquivos de 10 MB.</div>
            </div>

            <div class="text-end"><button type="submit" class="btn btn-primary"><i class="bi bi-send" aria-hidden="true"></i> Enviar chamado</button></div>
        </form>
    </x-card>

    <div class="card">
        <ul class="list-group list-group-flush">
            @forelse ($tickets as $ticket)
                <li class="list-group-item">
                    <a href="{{ route('portal.tickets.show', $ticket) }}" class="d-flex align-items-center justify-content-between gap-3 text-body text-decoration-none">
                        <span class="min-w-0">
                            <span class="d-block fw-medium text-truncate">#{{ $ticket->id }} · {{ $ticket->title }}</span>
                            <span class="d-block small text-body-secondary">Aberto em {{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                        </span>
                        <x-badge :color="$ticket->status->color()">{{ $ticket->status->label() }}</x-badge>
                    </a>
                </li>
            @empty
                <li class="list-group-item text-center text-body-secondary py-4">Você ainda não abriu nenhum chamado.</li>
            @endforelse
        </ul>
    </div>

    <div class="mt-3">{{ $tickets->links() }}</div>
</x-layouts.portal>
