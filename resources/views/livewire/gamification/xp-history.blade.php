<div class="rounded-xl border border-line/60 bg-card p-4 shadow-sm">
    <h3 class="mb-3 text-sm font-semibold text-ink">Atividade recente</h3>

    @forelse ($transactions as $transaction)
        <div class="flex items-center justify-between border-b border-line/50 py-2 text-sm last:border-0" wire:key="xp-{{ $transaction->id }}">
            <span class="text-ink-muted">{{ $transaction->description }}</span>
            <x-xp-badge :amount="$transaction->amount" />
        </div>
    @empty
        <p class="text-sm text-ink-muted">Nenhuma atividade ainda.</p>
    @endforelse

    @if ($paginated)
        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <x-per-page-selector />
            {{ $transactions->links() }}
        </div>
    @endif
</div>
