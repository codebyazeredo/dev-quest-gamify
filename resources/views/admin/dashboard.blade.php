@php
    $indicators = $overview['indicators'];
    $statusMax = max(1, collect($overview['byStatus'])->max('total'));
    $xpMax = max(1, collect($overview['xpPerDay'])->max('total'));
    $categoryTotal = max(1, $overview['byCategory']->sum('total'));
    $priorityTotal = max(1, $overview['byPriority']->sum('total'));
@endphp

<x-layouts.app>
    <div>
        <x-page-header title="Painel administrativo" subtitle="Visão geral da operação e da gamificação do time." />

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3"><x-stat-tile icon="columns" color="primary" label="Tarefas abertas" :value="number_format($indicators['openTasks'])" /></div>
            <div class="col-6 col-lg-3"><x-stat-tile icon="alert" color="danger" label="Tarefas atrasadas" :value="number_format($indicators['lateTasks'])" /></div>
            <div class="col-6 col-lg-3"><x-stat-tile icon="check" color="success" label="Concluídas hoje" :value="number_format($indicators['completedToday'])" /></div>
            <div class="col-6 col-lg-3"><x-stat-tile icon="bolt" color="warning" label="XP distribuído hoje" :value="number_format($indicators['xpToday'])" /></div>
            <div class="col-6 col-lg-3"><x-stat-tile icon="users" color="info" label="Desenvolvedores" :value="number_format($indicators['developers'])" /></div>
            <div class="col-6 col-lg-3"><x-stat-tile icon="fire" color="danger" label="Ativos na semana" :value="number_format($indicators['activeUsers'])" /></div>
            <div class="col-6 col-lg-3"><x-stat-tile icon="tag" color="secondary" label="Sem responsável" :value="number_format($indicators['unassignedTasks'])" /></div>
            <div class="col-6 col-lg-3"><x-stat-tile icon="trophy" color="success" label="Concluídas na semana" :value="number_format($indicators['completedThisWeek'])" /></div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header fw-semibold"><i class="bi bi-bar-chart me-1" aria-hidden="true"></i> XP distribuído nos últimos 7 dias</div>
                    <div class="card-body">
                        <div class="d-flex align-items-end gap-2" style="height: 12rem;" role="img" aria-label="Gráfico de barras de XP por dia">
                            @foreach ($overview['xpPerDay'] as $day)
                                <div class="flex-fill d-flex flex-column align-items-center justify-content-end h-100 min-w-0">
                                    <span class="small fw-semibold mb-1">{{ number_format($day['total']) }}</span>
                                    <div class="w-100 rounded-top bg-primary" style="height: {{ max(2, round($day['total'] / $xpMax * 100)) }}%; opacity: {{ $day['total'] > 0 ? 1 : 0.25 }};"></div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            @foreach ($overview['xpPerDay'] as $day)
                                <div class="flex-fill text-center small text-body-secondary">{{ $day['label'] }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header fw-semibold"><i class="bi bi-trophy me-1" aria-hidden="true"></i> Top da semana</div>
                    <ul class="list-group list-group-flush">
                        @forelse ($overview['topUsers'] as $index => $top)
                            <li class="list-group-item d-flex align-items-center gap-3">
                                <span class="fw-bold text-body-secondary" style="width: 1.5rem;">{{ $index + 1 }}º</span>
                                <span class="flex-grow-1 text-truncate">{{ $top->name }}</span>
                                <x-badge color="success">{{ number_format($top->total) }} XP</x-badge>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-body-secondary py-5">Ninguém pontuou nesta semana ainda.</li>
                        @endforelse
                    </ul>
                    <div class="card-footer bg-body-tertiary text-end">
                        <a href="{{ route('ranking') }}" class="small">Ver ranking completo <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header fw-semibold">Tarefas por status</div>
                    <div class="card-body vstack gap-3">
                        @foreach ($overview['byStatus'] as $row)
                            <div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>{{ $row['label'] }}</span>
                                    <span class="fw-semibold">{{ $row['total'] }}</span>
                                </div>
                                <div class="progress" role="progressbar" aria-valuenow="{{ $row['total'] }}" aria-valuemin="0" aria-valuemax="{{ $statusMax }}" aria-label="{{ $row['label'] }}">
                                    <div class="progress-bar" style="width: {{ round($row['total'] / $statusMax * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header fw-semibold">Tarefas por categoria</div>
                    <div class="card-body vstack gap-3">
                        @forelse ($overview['byCategory'] as $row)
                            <div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle d-inline-block" style="width: .65rem; height: .65rem; background-color: {{ $row->category?->color }}; border: 1px solid var(--bs-border-color);"></span>
                                        {{ $row->category?->name ?? 'Sem categoria' }}
                                    </span>
                                    <span class="fw-semibold">{{ $row->total }}</span>
                                </div>
                                <div class="progress" role="progressbar" aria-valuenow="{{ $row->total }}" aria-valuemin="0" aria-valuemax="{{ $categoryTotal }}">
                                    <div class="progress-bar" style="width: {{ round($row->total / $categoryTotal * 100) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-body-secondary text-center py-4 mb-0">Sem tarefas cadastradas.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header fw-semibold">Tarefas por prioridade</div>
                    <div class="card-body vstack gap-3">
                        @forelse ($overview['byPriority'] as $row)
                            <div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>{{ $row->priority?->name ?? 'Sem prioridade' }}</span>
                                    <span class="fw-semibold">{{ $row->total }}</span>
                                </div>
                                <div class="progress" role="progressbar" aria-valuenow="{{ $row->total }}" aria-valuemin="0" aria-valuemax="{{ $priorityTotal }}">
                                    <div class="progress-bar" style="width: {{ round($row->total / $priorityTotal * 100) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-body-secondary text-center py-4 mb-0">Sem tarefas cadastradas.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
