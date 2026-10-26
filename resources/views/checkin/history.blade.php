<x-layouts.app>
    <div>
        <x-page-header title="Check-in" subtitle="Faça check-in todos os dias para manter sua sequência." />

        <div class="row g-4 justify-content-center">
            <div class="col-lg-6 col-xl-5">
                <x-checkin-button :checked-in-today="$checkedInToday" :current-streak="$currentStreak" />
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-lg-8 col-xl-6">
                <x-card padding="p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="h5 mb-0 text-capitalize">{{ $monthLabel }}</h2>

                        <div class="d-flex align-items-center gap-1" role="group" aria-label="Navegar entre meses">
                            <a href="{{ route('checkin', ['month' => $previousMonth]) }}" title="Mês anterior" aria-label="Mês anterior" class="btn btn-ghost btn-icon btn-icon-sm">
                                <i class="bi bi-chevron-left" aria-hidden="true"></i>
                            </a>
                            <a href="{{ route('checkin') }}" class="btn btn-ghost btn-icon btn-icon-sm" data-bs-toggle="tooltip" data-bs-title="Ir para o mês atual" aria-label="Ir para o mês atual"><i class="bi bi-calendar-check" aria-hidden="true"></i></a>
                            <a href="{{ route('checkin', ['month' => $nextMonth]) }}" title="Próximo mês" aria-label="Próximo mês" class="btn btn-ghost btn-icon btn-icon-sm">
                                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>

                    <div class="d-grid text-center small fw-semibold text-body-secondary text-uppercase mb-2" style="grid-template-columns: repeat(7, 1fr); gap: 0.5rem;">
                        <div>Seg</div>
                        <div>Ter</div>
                        <div>Qua</div>
                        <div>Qui</div>
                        <div>Sex</div>
                        <div>Sáb</div>
                        <div>Dom</div>
                    </div>

                    <div class="d-grid" style="grid-template-columns: repeat(7, 1fr); gap: 0.5rem;">
                        @foreach ($days as $day)
                            <div
                                class="checkin-day d-flex align-items-center justify-content-center rounded-3 border
                                    {{ $day['checkedIn'] ? 'bg-success-subtle border-success-subtle text-success-emphasis' : ($day['inMonth'] ? 'bg-body' : 'bg-body-tertiary border-0') }}
                                    {{ $day['isToday'] ? 'checkin-day--today' : '' }}"
                            >
                                @if ($day['checkedIn'])
                                    <x-icon name="check" class="fs-5 fw-bold" />
                                @else
                                    <span class="{{ $day['inMonth'] ? '' : 'text-body-tertiary' }}">{{ $day['date']->day }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex align-items-center justify-content-center gap-4 small text-body-secondary mt-3">
                        <span class="d-flex align-items-center gap-2"><span class="badge bg-success-subtle text-success-emphasis"><x-icon name="check" /></span> Feito</span>
                        <span class="d-flex align-items-center gap-2"><span class="badge border text-body-secondary">&nbsp;</span> Sem check-in</span>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-layouts.app>
