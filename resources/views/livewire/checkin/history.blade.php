<div>
    <h1 class="mb-6 text-2xl font-bold tracking-tight text-ink">Check-in</h1>

    <livewire:checkin.button />

    <div class="mt-6 w-full max-w-xs rounded-xl border border-line/60 bg-card p-3 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
            <h2 class="text-xs font-semibold text-ink">{{ $monthLabel }}</h2>

            <div class="flex items-center gap-0.5">
                <button type="button" wire:click="previousMonth" title="Mês anterior" aria-label="Mês anterior" class="rounded-md p-1 text-ink-muted hover:bg-line/20">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M15 6l-6 6 6 6" /></svg>
                </button>
                <button type="button" wire:click="goToCurrentMonth" class="rounded-md px-1.5 py-1 text-[10px] font-medium text-ink hover:bg-line/20">
                    Hoje
                </button>
                <button type="button" wire:click="nextMonth" title="Próximo mês" aria-label="Próximo mês" class="rounded-md p-1 text-ink-muted hover:bg-line/20">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M9 6l6 6-6 6" /></svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-medium text-ink-muted">
            <div>S</div>
            <div>T</div>
            <div>Q</div>
            <div>Q</div>
            <div>S</div>
            <div>S</div>
            <div>D</div>
        </div>

        <div class="mt-1 grid grid-cols-7 gap-1">
            @foreach ($days as $day)
                <div
                    wire:key="day-{{ $day['date']->toDateString() }}"
                    class="flex h-7 flex-col items-center justify-center rounded-md border text-center
                        {{ $day['inMonth'] ? 'border-line bg-card' : 'border-transparent bg-line/10' }}
                        {{ $day['isToday'] ? 'ring-1 ring-primary' : '' }}"
                >
                    @if ($day['checkedIn'])
                        <span class="text-forest"><x-icon name="check" class="h-3 w-3" /></span>
                    @else
                        <span class="text-[10px] {{ $day['inMonth'] ? 'text-ink' : 'text-ink-muted/50' }}">
                            {{ $day['date']->day }}
                        </span>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-2 flex items-center gap-3 text-[10px] text-ink-muted">
            <span class="flex items-center gap-1"><span class="text-forest"><x-icon name="check" class="h-3 w-3" /></span> Feito</span>
            <span class="flex items-center gap-1"><span class="h-3 w-3 rounded-full border border-line"></span> Sem check-in</span>
        </div>
    </div>
</div>
