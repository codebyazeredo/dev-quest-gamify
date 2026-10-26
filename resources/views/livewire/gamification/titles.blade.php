<div>
    <x-page-header title="Títulos" subtitle="Escolha o título exibido no seu perfil e veja como desbloquear os próximos." :back="route('dashboard')" backLabel="Minha conta" />

    <h2 class="h6 text-body-secondary text-uppercase mb-3">Desbloqueados</h2>

    @if ($unlockedTitles->isEmpty())
        <div class="text-center text-body-secondary py-5">
            <i class="bi bi-patch-check fs-1 d-block mb-2" aria-hidden="true"></i>
            Você ainda não desbloqueou nenhum título.
        </div>
    @else
        <div class="row g-3">
            @foreach ($unlockedTitles as $title)
                <div class="col-12 col-md-6 col-xl-4">
                    <x-card :variant="$selectedTitleId === $title->id ? 'active' : 'default'" class="h-100">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <x-icon-chip :icon="$title->icon" color="warning" />
                                <div class="min-w-0">
                                    <div class="fw-semibold text-truncate">{{ $title->name }}</div>
                                    <div class="small text-body-secondary text-truncate">{{ $title->unlockRuleDescription() }}</div>
                                </div>
                            </div>

                            @if ($selectedTitleId === $title->id)
                                <x-icon-button wire:click="clearTitle" icon="x-circle" label="Remover título" class="btn-sm" />
                            @else
                                <x-icon-button wire:click="selectTitle({{ $title->id }})" icon="check2-circle" label="Usar este título" variant="primary" />
                            @endif
                        </div>
                    </x-card>
                </div>
            @endforeach
        </div>
    @endif

    @if ($lockedTitles->isNotEmpty())
        <h2 class="h6 text-body-secondary text-uppercase mt-5 mb-3">Para desbloquear</h2>

        <div class="row g-3">
            @foreach ($lockedTitles as $title)
                <div class="col-12 col-md-6 col-xl-4">
                    <x-card class="h-100">
                        <div class="d-flex align-items-start gap-3">
                            <x-icon-chip :icon="$title->icon" color="secondary" />
                            <div class="min-w-0 flex-grow-1">
                                <div class="fw-semibold">{{ $title->name }} <i class="bi bi-lock-fill text-body-tertiary" aria-hidden="true"></i></div>
                                <div class="small text-body-secondary">{{ $title->unlockRuleDescription() }}</div>

                                @if ($title->unlock_type === \App\Enums\TitleUnlockType::CHALLENGES_COUNT)
                                    <div class="mt-2">
                                        <x-progress-bar :value="min($completedChallenges, $title->challenges_required)" :max="$title->challenges_required" />
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-card>
                </div>
            @endforeach
        </div>
    @endif
</div>
