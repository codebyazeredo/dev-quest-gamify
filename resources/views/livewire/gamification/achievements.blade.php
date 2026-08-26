<div>
    <a href="{{ route('dashboard') }}" class="mb-4 inline-flex items-center gap-1 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
        ← Minha conta
    </a>

    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Conquistas</h1>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($achievements as $row)
            @php [$achievement, $unlocked, $progress] = [$row['achievement'], $row['unlocked'], $row['progress']]; @endphp

            <div class="rounded-lg border p-4 {{ $unlocked ? 'border-2 border-amber-300 bg-gradient-to-br from-amber-50 to-yellow-50 shadow-md shadow-amber-200/60 ring-1 ring-amber-200 dark:border-amber-700 dark:from-amber-900/30 dark:to-yellow-900/20 dark:shadow-none dark:ring-amber-800' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                <div class="flex items-center gap-2">
                    <span class="{{ $unlocked ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500' }}">
                        <x-icon :name="$achievement->icon" class="h-7 w-7" />
                    </span>
                    <div>
                        <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $achievement->name }}</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $achievement->description }}</p>
                    </div>
                </div>

                <div class="mt-3">
                    <x-progress-bar :value="min($progress, $achievement->condition_value)" :max="$achievement->condition_value" />
                </div>

                <p class="mt-2 text-xs {{ $unlocked ? 'font-semibold text-amber-600 dark:text-amber-300' : 'text-gray-400' }}">
                    {{ $unlocked ? 'Desbloqueada' : '+'.$achievement->xp_reward.' XP' }}
                </p>
            </div>
        @endforeach
    </div>
</div>
