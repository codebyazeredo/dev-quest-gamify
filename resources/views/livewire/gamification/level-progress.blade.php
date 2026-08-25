<div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
    <div class="mb-2 flex items-center justify-between">
        <x-level-badge :level="$currentLevel->level" />
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($totalXp) }} XP total</span>
    </div>

    @if ($nextLevel)
        <x-progress-bar :value="$xpIntoLevel" :max="$xpForNext" />
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ number_format(max(0, $nextLevel->xp_required - $totalXp)) }} XP para o Nível {{ $nextLevel->level }}
        </p>
    @else
        <p class="text-xs text-gray-500 dark:text-gray-400">Nível máximo atingido.</p>
    @endif
</div>
