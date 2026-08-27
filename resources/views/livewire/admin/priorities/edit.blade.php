<x-modal title="Editar gravidade">
    <form wire:submit="save" class="space-y-4">
        <x-input name="name" label="Nome" wire:model="name" autofocus />

        <x-input name="multiplier" label="Multiplicador de XP" type="number" min="0" step="0.01" wire:model="multiplier" />

        <div class="flex justify-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Salvar alterações</x-button>
        </div>
    </form>
</x-modal>
