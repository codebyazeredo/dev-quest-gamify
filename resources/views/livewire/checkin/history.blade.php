<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Check-in</h1>

    <livewire:checkin.button />

    <div class="mt-6">
        <h2 class="mb-3 text-sm font-semibold text-gray-800 dark:text-gray-100">Last 14 days</h2>

        <div class="grid grid-cols-7 gap-2">
            @foreach ($days as $day)
                <div class="flex flex-col items-center rounded-md border border-gray-100 p-2 text-center dark:border-gray-700" wire:key="day-{{ $day['date']->toDateString() }}">
                    <span class="text-xs text-gray-400">{{ $day['date']->format('d/m') }}</span>
                    <span class="mt-1 flex h-5 items-center justify-center text-lg text-gray-400">
                        @if ($day['checkedIn'])
                            <span class="text-emerald-500"><x-icon name="check" class="h-4 w-4" /></span>
                        @else
                            &mdash;
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
