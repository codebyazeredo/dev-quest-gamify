<div>
    <a href="{{ route('dashboard') }}" class="mb-4 inline-flex items-center gap-1 rounded-lg border border-line px-3 py-1.5 text-sm font-medium text-ink hover:bg-line/20">
        ← Minha conta
    </a>

    <h1 class="mb-6 text-2xl font-bold tracking-tight text-ink">Conquistas</h1>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($achievements as $row)
            @php [$achievement, $unlocked, $progress] = [$row['achievement'], $row['unlocked'], $row['progress']]; @endphp

            <div class="{{ $unlocked ? 'rounded-xl border-2 border-gold/40 bg-gold/5 p-4 shadow-md shadow-gold/10 ring-1 ring-gold/20' : 'rounded-xl border border-line bg-card p-4 shadow-sm' }}">
                <div class="flex items-center gap-2">
                    <span class="{{ $unlocked ? 'text-gold' : 'text-ink-muted' }}">
                        <x-icon :name="$achievement->icon" class="h-7 w-7" />
                    </span>
                    <div>
                        <h2 class="font-semibold text-ink">{{ $achievement->name }}</h2>
                        <p class="text-xs text-ink-muted">{{ $achievement->description }}</p>
                    </div>
                </div>

                <div class="mt-3">
                    <x-progress-bar :value="min($progress, $achievement->condition_value)" :max="$achievement->condition_value" />
                </div>

                <p class="mt-2 text-xs {{ $unlocked ? 'font-semibold text-gold' : 'text-ink-muted' }}">
                    {{ $unlocked ? 'Desbloqueada' : '+'.$achievement->xp_reward.' XP' }}
                </p>
            </div>
        @endforeach
    </div>
</div>
