<div class="space-y-6">
    <x-page-header :title="auth()->user()->name">
        <x-slot:subtitle>
            @if ($selectedTitle)
                <span class="flex items-center gap-1.5">
                    <span class="text-gold"><x-icon :name="$selectedTitle->icon" class="h-4 w-4" /></span>
                    {{ $selectedTitle->name }}
                </span>
            @else
                {{ auth()->user()->getRoleNames()->map(fn ($role) => \App\Enums\UserRole::labelFor($role))->join(', ') }}
            @endif
        </x-slot:subtitle>

        <button type="button" wire:click="toggleEditProfile" title="Editar meus dados" aria-label="Editar meus dados" class="rounded-lg border border-line bg-card p-2 text-ink-muted hover:bg-line/20">
            <x-icon name="pencil" class="h-4 w-4" />
        </button>
    </x-page-header>

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
