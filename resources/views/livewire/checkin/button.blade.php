<div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Check-in diário</p>
            <p class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                <span class="text-orange-500"><x-icon name="fire" class="h-4 w-4" /></span>
                Sequência de {{ $currentStreak }} dias
            </p>
        </div>

        @if ($checkedInToday)
            <span class="flex items-center gap-1.5 rounded-md bg-green-50 px-3 py-1.5 text-sm text-green-700 dark:bg-green-900/40 dark:text-green-300">
                <x-icon name="check" class="h-4 w-4" />
                Check-in feito
            </span>
        @else
            <button type="button" wire:click="checkIn" class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-500">
                Fazer check-in
            </button>
        @endif
    </div>
</div>
