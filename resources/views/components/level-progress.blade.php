@props(['progress'])

<x-card class="level-hero">
    @if ($progress['participatesInLeveling'])
        <div class="d-flex align-items-center gap-3 mb-3">
            <span class="level-ring level-tier-{{ \App\Models\Level::tierOf($progress['currentLevel']->level) }}">
                <small>NÍVEL</small>
                <strong>{{ $progress['currentLevel']->level }}</strong>
            </span>

            <div>
                <p class="fw-semibold fs-5 mb-0">{{ number_format($progress['totalXp']) }} XP</p>
                <p class="small text-body-secondary mb-0">acumulados no total</p>
            </div>
        </div>

        @if ($progress['nextLevel'])
            <x-progress-bar :value="$progress['xpIntoLevel']" :max="$progress['xpForNext']" color="gold" />
            <p class="small text-body-secondary mt-2 mb-0">
                Faltam <strong class="text-body">{{ number_format(max(0, $progress['nextLevel']->xp_required - $progress['totalXp'])) }} XP</strong> para o Nível {{ $progress['nextLevel']->level }}
            </p>
        @else
            <p class="small text-body-secondary mb-0"><i class="bi bi-stars text-warning" aria-hidden="true"></i> Nível máximo atingido.</p>
        @endif
    @else
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="fw-semibold">Sem progressão de nível</span>
            <span class="small text-body-secondary">{{ number_format($progress['totalXp']) }} XP total</span>
        </div>
        <p class="small text-body-secondary mb-0">
            Esta função não participa da subida de nível — ela é reservada para quem executa as tarefas (Desenvolvedor, Testador, Suporte), como incentivo ao bom desempenho.
        </p>
    @endif
</x-card>
