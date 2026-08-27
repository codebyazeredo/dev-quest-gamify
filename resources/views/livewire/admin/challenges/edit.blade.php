<x-modal title="Editar desafio" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
            <x-input name="name" label="Nome" wire:model="name" autofocus />

            <x-select name="type" label="Tipo" wire:model="type">
                @foreach ($challengeTypes as $challengeType)
                    <option value="{{ $challengeType->value }}">{{ $challengeType->label() }}</option>
                @endforeach
            </x-select>
        </div>

        <x-textarea name="description" label="Descrição" wire:model="description" rows="2" />

        <div class="grid grid-cols-4 gap-3">
            <x-input name="target" label="Meta" type="number" min="1" wire:model="target" />

            <x-input name="xp_reward" label="Recompensa de XP" type="number" min="0" wire:model="xp_reward" />

            <x-input name="starts_at" label="Início" type="datetime-local" wire:model="starts_at" />

            <x-input name="ends_at" label="Fim" type="datetime-local" wire:model="ends_at" />
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
