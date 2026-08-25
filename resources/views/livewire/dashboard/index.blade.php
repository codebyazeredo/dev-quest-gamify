<div class="space-y-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ auth()->user()->name }}</h1>
        @if ($selectedTitle)
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $selectedTitle->icon }} {{ $selectedTitle->name }}</p>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->role->label() }}</p>
        @endif
    </div>

    <livewire:gamification.level-progress />

    <livewire:checkin.button />

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">Tasks completed</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $tasksCompleted }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">XP this week</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ number_format($xpThisWeek) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">Ranking</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">#{{ $rankingPosition }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total XP</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ number_format($totalXp) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">Achievements</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $achievementsCount }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">Titles</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $titlesCount }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">Streak</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">🔥 {{ $currentStreak }}</p>
        </div>
    </div>

    <livewire:gamification.xp-history :limit="5" />
</div>
