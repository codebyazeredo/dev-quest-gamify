<div class="card">
    <div class="card-header bg-body fw-semibold"><i class="bi bi-activity me-1" aria-hidden="true"></i> Atividade recente</div>

    <ul class="list-group list-group-flush">
        @forelse ($transactions as $transaction)
            <li class="list-group-item d-flex align-items-center justify-content-between gap-3" wire:key="xp-{{ $transaction->id }}">
                <span class="small text-body-secondary">{{ $transaction->description }}</span>
                <x-xp-badge :amount="$transaction->amount" />
            </li>
        @empty
            <li class="list-group-item small text-body-secondary py-4 text-center">Nenhuma atividade ainda.</li>
        @endforelse
    </ul>

    @if ($paginated)
        <div class="card-footer bg-body d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <x-per-page-selector />
            {{ $transactions->links() }}
        </div>
    @endif
</div>
