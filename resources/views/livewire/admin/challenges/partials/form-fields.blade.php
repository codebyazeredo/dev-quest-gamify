<div class="row g-3">
    <div class="col-sm-7">
        <x-input name="name" label="Nome" wire:model="name" autofocus />
    </div>

    <div class="col-sm-5">
        <x-select name="type" label="O que conta como progresso" wire:model="type">
            @foreach ($challengeTypes as $challengeType)
                <option value="{{ $challengeType->value }}">{{ $challengeType->label() }}</option>
            @endforeach
        </x-select>
    </div>
</div>

<x-textarea name="description" label="Descrição" wire:model="description" rows="2" />

<div>
    <span class="form-label fw-medium small d-block mb-1">Ícone</span>
    <x-icon-picker model="icon" :icons="$icons" />
    @error('icon') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

<div class="row g-3">
    <div class="col-sm-6">
        <x-input name="target" label="Meta" type="number" min="1" wire:model="target" />
    </div>

    <div class="col-sm-6">
        <x-input name="xp_reward" label="Recompensa de XP" type="number" min="0" wire:model="xp_reward" />
    </div>
</div>

<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
        <span class="form-label fw-medium small mb-0">Período</span>
        <div class="btn-group btn-group-sm" role="group" aria-label="Atalhos de período">
            <button type="button" class="btn btn-outline-secondary" wire:click="applyPeriod('today')">Hoje</button>
            <button type="button" class="btn btn-outline-secondary" wire:click="applyPeriod('week')">Esta semana</button>
            <button type="button" class="btn btn-outline-secondary" wire:click="applyPeriod('next7')">Próx. 7 dias</button>
            <button type="button" class="btn btn-outline-secondary" wire:click="applyPeriod('month')">Este mês</button>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-sm-6">
            <x-input name="starts_at" label="Início" type="datetime-local" wire:model="starts_at" />
        </div>

        <div class="col-sm-6">
            <x-input name="ends_at" label="Fim" type="datetime-local" wire:model="ends_at" />
        </div>
    </div>
</div>

<div class="rounded-3 border p-3 d-flex align-items-center gap-3 bg-body-tertiary">
    <x-icon-chip :icon="$icon ?: 'flag'" color="primary" size="lg" />
    <div class="min-w-0">
        <div class="fw-semibold text-truncate">{{ $name !== '' ? $name : 'Nome do desafio' }}</div>
        <div class="small text-body-secondary">
            Meta de {{ $target }} · +{{ $xp_reward }} XP
            @if ($starts_at !== '' && $ends_at !== '')
                · {{ \Illuminate\Support\Carbon::parse($starts_at)->format('d/m') }} a {{ \Illuminate\Support\Carbon::parse($ends_at)->format('d/m') }}
            @endif
        </div>
    </div>
</div>
