<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink">{{ auth()->user()->name }}</h1>
            @if ($selectedTitle)
                <p class="flex items-center gap-1.5 text-sm text-ink-muted">
                    <span class="text-gold"><x-icon :name="$selectedTitle->icon" class="h-4 w-4" /></span>
                    {{ $selectedTitle->name }}
                </p>
            @else
                <p class="text-sm text-ink-muted">{{ auth()->user()->getRoleNames()->map(fn ($role) => \App\Enums\UserRole::labelFor($role))->join(', ') }}</p>
            @endif
        </div>

        <button type="button" wire:click="toggleEditProfile" title="Editar meus dados" aria-label="Editar meus dados" class="shrink-0 rounded-lg border border-line bg-card p-2 text-ink-muted hover:bg-line/20">
            <x-icon name="pencil" class="h-4 w-4" />
        </button>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('achievements') }}" class="rounded-lg border border-line bg-card px-3 py-1.5 text-sm font-medium text-ink hover:bg-line/20">
            Conquistas
        </a>
        <a href="{{ route('titles') }}" class="rounded-lg border border-line bg-card px-3 py-1.5 text-sm font-medium text-ink hover:bg-line/20">
            Títulos
        </a>
    </div>

    <livewire:gamification.level-progress />

    <livewire:checkin.button />

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-line/60 bg-card p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <x-icon-chip icon="check" color="forest" size="sm" />
                <div>
                    <p class="text-xs text-ink-muted">Tarefas concluídas</p>
                    <p class="text-lg font-semibold text-ink">{{ $tasksCompleted }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-line/60 bg-card p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <x-icon-chip icon="bolt" color="accent" size="sm" />
                <div>
                    <p class="text-xs text-ink-muted">XP nesta semana</p>
                    <p class="text-lg font-semibold text-ink">{{ number_format($xpThisWeek) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-line/60 bg-card p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <x-icon-chip icon="medal" color="gold" size="sm" />
                <div>
                    <p class="text-xs text-ink-muted">Ranking</p>
                    <p class="text-lg font-semibold text-ink">#{{ $rankingPosition }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-line/60 bg-card p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <x-icon-chip icon="star" color="gold" size="sm" />
                <div>
                    <p class="text-xs text-ink-muted">XP total</p>
                    <p class="text-lg font-semibold text-ink">{{ number_format($totalXp) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-line/60 bg-card p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <x-icon-chip icon="trophy" color="gold" size="sm" />
                <div>
                    <p class="text-xs text-ink-muted">Conquistas</p>
                    <p class="text-lg font-semibold text-ink">{{ $achievementsCount }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-line/60 bg-card p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <x-icon-chip icon="rocket" color="accent" size="sm" />
                <div>
                    <p class="text-xs text-ink-muted">Títulos</p>
                    <p class="text-lg font-semibold text-ink">{{ $titlesCount }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-line/60 bg-card p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <x-icon-chip icon="fire" color="amber-clay" size="sm" />
                <div>
                    <p class="text-xs text-ink-muted">Sequência</p>
                    <p class="text-lg font-semibold text-ink">{{ $currentStreak }}</p>
                </div>
            </div>
        </div>
    </div>

    <livewire:gamification.xp-history :limit="5" />

    @if ($showEditProfile)
        <livewire:profile.edit wire:key="profile-edit" />
    @endif
</div>
