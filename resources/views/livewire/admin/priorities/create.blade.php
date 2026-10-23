<x-modal title="Nova gravidade">
    <form wire:submit="save" class="vstack gap-3">
        <x-input name="name" label="Nome" wire:model="name" autofocus />

        <x-input name="multiplier" label="Multiplicador de XP" type="number" min="0" step="0.01" wire:model="multiplier" />

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar gravidade</x-button>
        </div>
    </form>
</x-modal>
