@props(['icons', 'award' => false, 'existing' => collect(), 'source' => 'desafio'])

<div class="rounded-3 border bg-body-tertiary p-3">
    <div class="form-check form-switch mb-0">
        <input class="form-check-input" type="checkbox" role="switch" id="award-title-switch" wire:model.live="award_title">
        <label class="form-check-label fw-medium" for="award-title-switch">
            <i class="bi bi-patch-check me-1" aria-hidden="true"></i>
            {{ $existing->isEmpty() ? 'Conceder um título ao concluir' : 'Criar mais um título para este '.$source }}
        </label>
    </div>

    @if ($existing->isNotEmpty())
        <div class="small text-body-secondary mt-2">
            Títulos já ligados:
            @foreach ($existing as $linkedTitle)
                <x-badge color="warning" class="ms-1"><x-icon :name="$linkedTitle->icon ?? 'medal'" /> {{ $linkedTitle->name }}</x-badge>
            @endforeach
        </div>
    @endif

    @if ($award)
        <div class="vstack gap-3 mt-3">
            <x-input name="reward_title_name" label="Nome do título" wire:model="reward_title_name" placeholder="Ex.: Caçador de Bugs" />

            <div>
                <span class="form-label fw-medium small d-block mb-1">Ícone do título</span>
                <x-icon-picker model="reward_title_icon" :icons="$icons" />
            </div>

            <div class="form-text mt-0">O título é criado ao salvar e concedido a quem concluir este {{ $source }}. Usuários que já cumpriram a regra também recebem.</div>
        </div>
    @endif
</div>
