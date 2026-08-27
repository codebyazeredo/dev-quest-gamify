<div>
    <x-page-header title="Configurações" />

    <div class="space-y-8">
        @foreach ($sections as $section)
            <section>
                <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-ink-muted">{{ $section['label'] }}</h2>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($section['items'] as $item)
                        <a href="{{ route($item['route']) }}" class="flex items-start gap-3 rounded-xl border border-line bg-card p-4 shadow-sm transition-colors hover:border-primary/40 hover:bg-primary/5">
                            <x-icon-chip :icon="$item['icon']" />
                            <span>
                                <span class="block text-sm font-semibold text-ink">{{ $item['label'] }}</span>
                                <span class="block text-xs text-ink-muted">{{ $item['description'] }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</div>
