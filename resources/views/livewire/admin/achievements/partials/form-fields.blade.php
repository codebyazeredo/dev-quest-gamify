<x-input name="name" label="Nome" wire:model="name" autofocus />

<div>
    <span class="form-label fw-medium small d-block mb-1">Ícone</span>
    <x-icon-picker model="icon" :icons="$icons" />
    @error('icon') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

<x-textarea name="description" label="Descrição" wire:model="description" rows="2" />

<div class="row g-3">
    <div class="col-sm-6">
        <x-select name="condition_type" label="Condição" wire:model="condition_type">
            @foreach ($conditionTypes as $type)
                <option value="{{ $type->value }}">{{ $type->label() }}</option>
            @endforeach
        </x-select>
    </div>

    <div class="col-sm-3">
        <x-input name="condition_value" label="Valor" type="number" min="1" wire:model="condition_value" />
    </div>

    <div class="col-sm-3">
        <x-input name="xp_reward" label="XP" type="number" min="0" wire:model="xp_reward" />
    </div>
</div>

<div class="rounded-3 border p-3 d-flex align-items-center gap-3 bg-body-tertiary">
    <x-icon-chip :icon="$icon ?: 'trophy'" color="warning" size="lg" />
    <div class="min-w-0">
        <div class="fw-semibold text-truncate">{{ $name !== '' ? $name : 'Nome da conquista' }}</div>
        <div class="small text-body-secondary">{{ $description !== '' ? $description : 'Descrição da conquista' }}</div>
        <x-badge class="mt-1">+{{ $xp_reward }} XP</x-badge>
    </div>
</div>
