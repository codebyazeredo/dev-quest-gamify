<x-modal title="Editar desafio" size="lg">
    <form wire:submit="save" class="vstack gap-3">
        <div class="form-grid form-grid-2">
            <x-input name="name" label="Nome" wire:model="name" autofocus />

            <x-select name="type" label="Tipo" wire:model="type">
                @foreach ($challengeTypes as $challengeType)
                    <option value="{{ $challengeType->value }}">{{ $challengeType->label() }}</option>
                @endforeach
            </x-select>
        </div>

        <x-textarea name="description" label="Descrição" wire:model="description" rows="2" />

        <div class="form-grid form-grid-4">
            <x-input name="target" label="Meta" type="number" min="1" wire:model="target" />

            <x-input name="xp_reward" label="Recompensa de XP" type="number" min="0" wire:model="xp_reward" />

            <x-input name="starts_at" label="Início" type="datetime-local" wire:model="starts_at" />

            <x-input name="ends_at" label="Fim" type="datetime-local" wire:model="ends_at" />
        </div>

        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="active-switch" wire:model="active">
            <label class="form-check-label" for="active-switch">Ativo</label>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Salvar alterações</x-button>
        </div>
    </form>
</x-modal>
