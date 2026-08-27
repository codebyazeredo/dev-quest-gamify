<x-modal title="Editar conquista" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <x-input name="name" label="Nome" wire:model="name" autofocus />

        <div>
            <label class="block text-sm font-medium text-ink">Ícone</label>
            <div class="mt-1">
                <x-icon-picker model="icon" :icons="$icons" />
            </div>
            @error('icon') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <x-textarea name="description" label="Descrição" wire:model="description" rows="2" />

        <div class="grid grid-cols-3 gap-3">
            <x-select name="condition_type" label="Condição" wire:model="condition_type">
                @foreach ($conditionTypes as $type)
                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                @endforeach
            </x-select>

            <x-input name="condition_value" label="Valor" type="number" min="1" wire:model="condition_value" />

            <x-input name="xp_reward" label="Recompensa de XP" type="number" min="0" wire:model="xp_reward" />
        </div>

        <label class="flex items-center gap-2 text-sm text-ink-muted">
            <input type="checkbox" wire:model="active" class="rounded border-line">
            Ativo
        </label>

        <div class="flex justify-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Salvar alterações</x-button>
        </div>
    </form>
</x-modal>
