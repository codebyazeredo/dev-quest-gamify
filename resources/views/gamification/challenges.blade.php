<x-layouts.app>
    <div>
        <x-page-header title="Desafios" subtitle="Metas por período com recompensa em XP e títulos." />

        <div class="row g-3">
            @foreach ($challenges as $row)
                @php [$challenge, $progress, $completed] = [$row['challenge'], $row['progress'], $row['completed']]; @endphp

                <div class="col-12 col-lg-6">
                    <x-card :variant="$completed ? 'active' : 'default'" class="h-100">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex align-items-start gap-3 min-w-0">
                                <x-icon-chip :icon="$challenge->icon ?? 'flag'" :color="$completed ? 'primary' : 'secondary'" />
                                <div class="min-w-0">
                                    <h2 class="h6 mb-1">{{ $challenge->name }}</h2>
                                    <p class="small text-body-secondary mb-0">{{ $challenge->description }}</p>
                                </div>
                            </div>
                            <div class="text-end small text-body-secondary text-nowrap">
                                <x-badge :color="$challenge->statusColor()">{{ $challenge->statusLabel() }}</x-badge>
                                <div class="mt-1"><i class="bi bi-calendar-event" aria-hidden="true"></i> até {{ $challenge->ends_at->format('d/m/Y') }}</div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <x-progress-bar :value="min($progress, $challenge->target)" :max="$challenge->target" />
                        </div>

                        <div class="d-flex flex-wrap gap-1 mt-2">
                            @if ($completed)
                                <x-badge color="primary"><x-icon name="check" /> Concluído</x-badge>
                            @else
                                <x-badge color="warning"><x-icon name="bolt" /> Recompensa: +{{ $challenge->xp_reward }} XP</x-badge>
                            @endif

                            @foreach ($challenge->titles as $rewardTitle)
                                <x-badge color="info"><x-icon :name="$rewardTitle->icon ?? 'medal'" /> Título: {{ $rewardTitle->name }}</x-badge>
                            @endforeach
                        </div>
                    </x-card>
                </div>
            @endforeach

            @if ($challenges->isEmpty())
                <div class="col-12 text-center text-body-secondary py-5">
                    <i class="bi bi-flag fs-1 d-block mb-2" aria-hidden="true"></i>
                    Nenhum desafio ativo no momento.
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
