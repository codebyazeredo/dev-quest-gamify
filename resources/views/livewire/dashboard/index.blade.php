<div class="space-y-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ auth()->user()->name }}</h1>
        @if ($selectedTitle)
            <p class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                <span class="text-amber-500"><x-icon :name="$selectedTitle->icon" class="h-4 w-4" /></span>
                {{ $selectedTitle->name }}
            </p>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->role->label() }}</p>
        @endif
    </div>

    <div class="flex gap-2">
        <a href="{{ route('achievements') }}" class="rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            Conquistas
        </a>
        <a href="{{ route('titles') }}" class="rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            Títulos
        </a>
    </div>

    <livewire:gamification.level-progress />

    <livewire:checkin.button />

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">Tarefas concluídas</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $tasksCompleted }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">XP nesta semana</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ number_format($xpThisWeek) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">Ranking</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">#{{ $rankingPosition }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">XP total</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ number_format($totalXp) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">Conquistas</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $achievementsCount }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">Títulos</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $titlesCount }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400">Sequência</p>
            <p class="flex items-center gap-1.5 text-lg font-semibold text-gray-800 dark:text-gray-100">
                <span class="text-orange-500"><x-icon name="fire" class="h-5 w-5" /></span>
                {{ $currentStreak }}
            </p>
        </div>
    </div>

    <livewire:gamification.xp-history :limit="5" />
</div>
