<x-card>
    @if ($participatesInLeveling)
        <div class="d-flex align-items-center justify-content-between mb-3">
            <x-level-badge :level="$currentLevel->level" />
            <span class="small text-body-secondary">{{ number_format($totalXp) }} XP total</span>
        </div>

        @if ($nextLevel)
            <x-progress-bar :value="$xpIntoLevel" :max="$xpForNext" color="gold" />
            <p class="small text-body-secondary mt-2 mb-0">
                Faltam <strong class="text-body">{{ number_format(max(0, $nextLevel->xp_required - $totalXp)) }} XP</strong> para o Nível {{ $nextLevel->level }}
            </p>
        @else
            <p class="small text-body-secondary mb-0"><i class="bi bi-stars text-warning" aria-hidden="true"></i> Nível máximo atingido.</p>
        @endif
    @else
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="fw-semibold">Sem progressão de nível</span>
            <span class="small text-body-secondary">{{ number_format($totalXp) }} XP total</span>
        </div>
        <p class="small text-body-secondary mb-0">
            Esta função não participa da subida de nível — ela é reservada para quem executa as tarefas (Desenvolvedor, Testador, Suporte), como incentivo ao bom desempenho.
        </p>
    @endif
</x-card>
