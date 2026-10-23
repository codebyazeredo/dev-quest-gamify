<div>
    <x-page-header title="Títulos" subtitle="Escolha o título exibido no seu perfil." :back="route('dashboard')" backLabel="Minha conta" />

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
                                <span class="fw-semibold text-truncate">{{ $title->name }}</span>
                            </div>

                            @if ($selectedTitleId === $title->id)
                                <button type="button" wire:click="clearTitle" class="btn btn-sm btn-outline-secondary bg-body">Remover</button>
                            @else
                                <button type="button" wire:click="selectTitle({{ $title->id }})" class="btn btn-sm btn-primary">Selecionar</button>
                            @endif
                        </div>
                    </x-card>
                </div>
            @endforeach
        </div>
    @endif
</div>
