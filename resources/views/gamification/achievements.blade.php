<x-layouts.app>
    <div>
        <x-page-header title="Conquistas" subtitle="Desbloqueie conquistas cumprindo metas e ganhe XP extra." :back="route('dashboard')" backLabel="Minha conta" />

        <div class="row g-3">
            @foreach ($achievements as $row)
                @php [$achievement, $unlocked, $progress] = [$row['achievement'], $row['unlocked'], $row['progress']]; @endphp

                <div class="col-12 col-md-6 col-xl-4">
                    <x-card :variant="$unlocked ? 'highlight' : 'default'" class="h-100">
                        <div class="d-flex align-items-start gap-3">
                            <x-icon-chip :icon="$achievement->icon" :color="$unlocked ? 'warning' : 'secondary'" size="lg" />
                            <div class="min-w-0">
                                <h2 class="h6 mb-1">{{ $achievement->name }}</h2>
                                <p class="small text-body-secondary mb-0">{{ $achievement->description }}</p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <x-progress-bar :value="min($progress, $achievement->condition_value)" :max="$achievement->condition_value" :color="$unlocked ? 'gold' : 'primary'" />
                        </div>

                        <div class="mt-2">
                            @if ($unlocked)
                                <x-badge color="warning"><i class="bi bi-unlock-fill" aria-hidden="true"></i> Desbloqueada</x-badge>
                            @else
                                <x-badge><i class="bi bi-lock-fill" aria-hidden="true"></i> +{{ $achievement->xp_reward }} XP</x-badge>
                            @endif
                        </div>
                    </x-card>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
