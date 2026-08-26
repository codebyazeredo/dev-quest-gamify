<div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
    @if ($participatesInLeveling)
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
    @else
        <div class="mb-1 flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Sem progressão de nível</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($totalXp) }} XP total</span>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Esta função não participa da subida de nível — ela é reservada para quem executa as tarefas (Desenvolvedor, Testador, Suporte), como incentivo ao bom desempenho.
        </p>
    @endif
</div>
