<div>
    <x-page-header title="Check-in" />

    <livewire:checkin.button />

    <x-card rounded="rounded-2xl" padding="p-6" class="mx-auto mt-6 w-full max-w-lg">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-ink">{{ $monthLabel }}</h2>

            <div class="flex items-center gap-1">
                <button type="button" wire:click="previousMonth" title="Mês anterior" aria-label="Mês anterior" class="rounded-lg p-2 text-ink-muted hover:bg-line/20">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M15 6l-6 6 6 6" /></svg>
                </button>
                <button type="button" wire:click="goToCurrentMonth" class="rounded-lg px-3 py-1.5 text-sm font-medium text-ink hover:bg-line/20">
                    Hoje
                </button>
                <button type="button" wire:click="nextMonth" title="Próximo mês" aria-label="Próximo mês" class="rounded-lg p-2 text-ink-muted hover:bg-line/20">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M9 6l6 6-6 6" /></svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-7 gap-2 text-center text-xs font-medium text-ink-muted">
            <div>Seg</div>
            <div>Ter</div>
            <div>Qua</div>
            <div>Qui</div>
            <div>Sex</div>
            <div>Sáb</div>
            <div>Dom</div>
        </div>

        <div class="mt-2 grid grid-cols-7 gap-2">
            @foreach ($days as $day)
                <div
                    wire:key="day-{{ $day['date']->toDateString() }}"
                    class="flex h-12 flex-col items-center justify-center rounded-lg border text-center sm:h-14
                        {{ $day['inMonth'] ? 'border-line bg-card' : 'border-transparent bg-line/10' }}
                        {{ $day['isToday'] ? 'ring-2 ring-primary' : '' }}"
                >
                    @if ($day['checkedIn'])
                        <span class="text-forest"><x-icon name="check" class="h-5 w-5" /></span>
                    @else
                        <span class="text-sm {{ $day['inMonth'] ? 'text-ink' : 'text-ink-muted/50' }}">
                            {{ $day['date']->day }}
                        </span>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4 flex items-center justify-center gap-4 text-sm text-ink-muted">
            <span class="flex items-center gap-1.5"><span class="text-forest"><x-icon name="check" class="h-4 w-4" /></span> Feito</span>
            <span class="flex items-center gap-1.5"><span class="h-4 w-4 rounded-full border border-line"></span> Sem check-in</span>
        </div>
    </x-card>
</div>
