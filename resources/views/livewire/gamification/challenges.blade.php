<div>
    <h1 class="mb-6 text-2xl font-bold tracking-tight text-ink">Desafios</h1>

    <div class="space-y-4">
        @foreach ($challenges as $row)
            @php [$challenge, $progress, $completed] = [$row['challenge'], $row['progress'], $row['completed']]; @endphp

            <div class="rounded-xl border p-4 {{ $completed ? 'border-primary/40 bg-primary/5' : 'border-line bg-card shadow-sm' }}">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-ink">{{ $challenge->name }}</h2>
                    <span class="text-xs text-ink-muted">termina em {{ $challenge->ends_at->format('d/m/Y') }}</span>
                </div>

                <p class="mt-1 text-sm text-ink-muted">{{ $challenge->description }}</p>

                <div class="mt-3">
                    <x-progress-bar :value="min($progress, $challenge->target)" :max="$challenge->target" />
                </div>

                <p class="mt-2 text-xs {{ $completed ? 'font-semibold text-primary' : 'text-ink-muted' }}">
                    {{ $completed ? 'Concluído' : 'Recompensa: +'.$challenge->xp_reward.' XP' }}
                </p>
            </div>
        @endforeach

        @if ($challenges->isEmpty())
            <p class="text-sm text-ink-muted">Nenhum desafio ativo no momento.</p>
        @endif
    </div>
</div>
