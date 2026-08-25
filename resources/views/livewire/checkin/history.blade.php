<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Check-in</h1>

    <livewire:checkin.button />

    <div class="mt-6">
        <h2 class="mb-3 text-sm font-semibold text-gray-800 dark:text-gray-100">Last 14 days</h2>

        <div class="grid grid-cols-7 gap-2">
            @foreach ($days as $day)
                <div class="flex flex-col items-center rounded-md border border-gray-100 p-2 text-center dark:border-gray-700" wire:key="day-{{ $day['date']->toDateString() }}">
                    <span class="text-xs text-gray-400">{{ $day['date']->format('d/m') }}</span>
                    <span class="mt-1 text-lg">{{ $day['checkedIn'] ? '✓' : '—' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
