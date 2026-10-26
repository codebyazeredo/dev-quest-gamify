<x-layouts.app>
    <div>
        <x-page-header title="Configurações" subtitle="Gerencie pessoas, regras de pontuação e a identidade do sistema." />

        @foreach ($sections as $section)
            <section class="mb-4">
                <h2 class="h6 text-body-secondary text-uppercase mb-3">{{ $section['label'] }}</h2>

                <div class="row g-3">
                    @foreach ($section['items'] as $item)
                        <div class="col-12 col-md-6 col-xl-4">
                            <a href="{{ route($item['route']) }}" class="card card-hover h-100 text-decoration-none text-body">
                                <div class="card-body d-flex align-items-start gap-3">
                                    <x-icon-chip :icon="$item['icon']" />
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="fw-semibold">{{ $item['label'] }}</div>
                                        <div class="small text-body-secondary">{{ $item['description'] }}</div>
                                    </div>
                                    <i class="bi bi-chevron-right text-body-tertiary align-self-center" aria-hidden="true"></i>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</x-layouts.app>
