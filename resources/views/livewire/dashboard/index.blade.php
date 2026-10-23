<div>
    <div class="card mb-4 overflow-hidden">
        <div class="bg-primary" style="height: 0.375rem;"></div>
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center gap-4">
            <x-avatar :user="auth()->user()" size="xl" />

            <div class="flex-grow-1 min-w-0">
                <p class="small text-uppercase fw-semibold text-body-secondary mb-1">Minha conta</p>
                <h1 class="h3 mb-1 text-truncate">{{ auth()->user()->name }}</h1>
                <p class="mb-0 d-flex align-items-center gap-2 text-body-secondary">
                    @if ($selectedTitle)
                        <x-icon :name="$selectedTitle->icon" />
                        {{ $selectedTitle->name }}
                    @else
                        {{ auth()->user()->getRoleNames()->map(fn ($role) => \App\Enums\UserRole::labelFor($role))->join(', ') }}
                    @endif
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('achievements') }}" class="btn btn-outline-secondary bg-body">
                    <i class="bi bi-award" aria-hidden="true"></i> Conquistas
                </a>
                <a href="{{ route('titles') }}" class="btn btn-outline-secondary bg-body">
                    <i class="bi bi-patch-check" aria-hidden="true"></i> Títulos
                </a>
                <button type="button" wire:click="toggleEditProfile" class="btn btn-primary">
                    <x-icon name="pencil" /> Editar dados
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4">
                    <x-stat-tile icon="check" color="success" label="Tarefas concluídas" :value="$tasksCompleted" />
                </div>
                <div class="col-6 col-md-4">
                    <x-stat-tile icon="bolt" color="info" label="XP nesta semana" :value="number_format($xpThisWeek)" />
                </div>
                <div class="col-6 col-md-4">
                    <x-stat-tile icon="medal" color="warning" label="Ranking" :value="'#'.$rankingPosition" />
                </div>
                <div class="col-6 col-md-4">
                    <x-stat-tile icon="star" color="warning" label="XP total" :value="number_format($totalXp)" />
                </div>
                <div class="col-6 col-md-4">
                    <x-stat-tile icon="trophy" color="primary" label="Conquistas" :value="$achievementsCount" />
                </div>
                <div class="col-6 col-md-4">
                    <x-stat-tile icon="rocket" color="info" label="Títulos" :value="$titlesCount" />
                </div>
            </div>

            <livewire:gamification.xp-history :limit="5" />
        </div>

        <div class="col-xl-4 vstack gap-4">
            <livewire:gamification.level-progress />

            <livewire:checkin.button />

            <x-stat-tile icon="fire" color="danger" label="Dias seguidos de check-in" :value="$currentStreak" />
        </div>
    </div>

    @if ($showEditProfile)
        <livewire:profile.edit wire:key="profile-edit" />
    @endif
</div>
