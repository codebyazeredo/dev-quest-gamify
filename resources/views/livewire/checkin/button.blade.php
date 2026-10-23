<x-card>
    <div class="d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <x-icon-chip icon="fire" color="danger" />
            <div>
                <p class="fw-semibold mb-0">Check-in diário</p>
                <p class="small text-body-secondary mb-0">Sequência de {{ $currentStreak }} dias</p>
            </div>
        </div>

        @if ($checkedInToday)
            <x-badge color="success" class="px-3 py-2">
                <x-icon name="check" /> Feito
            </x-badge>
        @else
            <button type="button" wire:click="checkIn" class="btn btn-primary btn-sm">
                Fazer check-in
            </button>
        @endif
    </div>
</x-card>
