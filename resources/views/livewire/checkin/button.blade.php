<div class="rounded-xl border border-line/60 bg-card p-4 shadow-sm">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <x-icon-chip icon="fire" color="amber-clay" size="sm" />
            <div>
                <p class="text-sm font-semibold text-ink">Check-in diário</p>
                <p class="text-xs text-ink-muted">Sequência de {{ $currentStreak }} dias</p>
            </div>
        </div>

        @if ($checkedInToday)
            <span class="flex items-center gap-1.5 rounded-lg bg-forest/10 px-3 py-1.5 text-sm font-medium text-forest">
                <x-icon name="check" class="h-4 w-4" />
                Check-in feito
            </span>
        @else
            <button type="button" wire:click="checkIn" class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-hover">
                Fazer check-in
            </button>
        @endif
    </div>
</div>
