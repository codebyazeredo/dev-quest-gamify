<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Achievements</h1>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($achievements as $row)
            @php [$achievement, $unlocked, $progress] = [$row['achievement'], $row['unlocked'], $row['progress']]; @endphp

            <div class="rounded-lg border p-4 {{ $unlocked ? 'border-indigo-300 bg-indigo-50 dark:border-indigo-700 dark:bg-indigo-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">{{ $achievement->icon }}</span>
                    <div>
                        <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $achievement->name }}</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $achievement->description }}</p>
                    </div>
                </div>

                <div class="mt-3">
                    <x-progress-bar :value="min($progress, $achievement->condition_value)" :max="$achievement->condition_value" />
                </div>

                <p class="mt-2 text-xs {{ $unlocked ? 'font-semibold text-indigo-600 dark:text-indigo-300' : 'text-gray-400' }}">
                    {{ $unlocked ? 'Unlocked' : '+'.$achievement->xp_reward.' XP' }}
                </p>
            </div>
        @endforeach
    </div>
</div>
