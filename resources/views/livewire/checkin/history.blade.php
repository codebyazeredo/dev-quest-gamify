<div>
    <h1 class="mb-6 text-2xl font-bold tracking-tight text-ink">Check-in</h1>

    <livewire:checkin.button />

    <div class="mt-6">
        <h2 class="mb-3 text-sm font-semibold text-ink">Últimos 14 dias</h2>

        <div class="grid grid-cols-7 gap-2">
            @foreach ($days as $day)
                <div class="flex flex-col items-center rounded-lg border border-line p-2 text-center" wire:key="day-{{ $day['date']->toDateString() }}">
                    <span class="text-xs text-ink-muted">{{ $day['date']->format('d/m') }}</span>
                    <span class="mt-1 flex h-5 items-center justify-center text-lg text-ink-muted">
                        @if ($day['checkedIn'])
                            <span class="text-forest"><x-icon name="check" class="h-4 w-4" /></span>
                        @else
                            &mdash;
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
