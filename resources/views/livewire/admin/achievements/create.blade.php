<x-modal title="Nova conquista" size="lg">
    <form wire:submit="save" class="vstack gap-3">
        <x-input name="name" label="Nome" wire:model="name" autofocus />

        <div>
            <label class="form-label fw-medium small mb-1">Ícone</label>
            <x-icon-picker model="icon" :icons="$icons" />
            @error('icon') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <x-textarea name="description" label="Descrição" wire:model="description" rows="2" />

        <div class="form-grid form-grid-3">
            <x-select name="condition_type" label="Condição" wire:model="condition_type">
                @foreach ($conditionTypes as $type)
                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                @endforeach
            </x-select>

            <x-input name="condition_value" label="Valor" type="number" min="1" wire:model="condition_value" />

            <x-input name="xp_reward" label="Recompensa de XP" type="number" min="0" wire:model="xp_reward" />
        </div>

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar conquista</x-button>
        </div>
    </form>
</x-modal>
