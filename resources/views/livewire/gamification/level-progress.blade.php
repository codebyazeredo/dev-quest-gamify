<div class="rounded-xl border border-line/60 bg-card p-4 shadow-sm">
    @if ($participatesInLeveling)
        <div class="mb-2 flex items-center justify-between">
            <x-level-badge :level="$currentLevel->level" />
            <span class="text-sm text-ink-muted">{{ number_format($totalXp) }} XP total</span>
        </div>

        @if ($nextLevel)
            <x-progress-bar :value="$xpIntoLevel" :max="$xpForNext" color="gold" />
            <p class="mt-1 text-xs text-ink-muted">
                {{ number_format(max(0, $nextLevel->xp_required - $totalXp)) }} XP para o Nível {{ $nextLevel->level }}
            </p>
        @else
            <p class="text-xs text-ink-muted">Nível máximo atingido.</p>
        @endif
    @else
        <div class="mb-1 flex items-center justify-between">
            <span class="text-sm font-medium text-ink">Sem progressão de nível</span>
            <span class="text-sm text-ink-muted">{{ number_format($totalXp) }} XP total</span>
        </div>
        <p class="text-xs text-ink-muted">
            Esta função não participa da subida de nível — ela é reservada para quem executa as tarefas (Desenvolvedor, Testador, Suporte), como incentivo ao bom desempenho.
        </p>
    @endif
</div>
