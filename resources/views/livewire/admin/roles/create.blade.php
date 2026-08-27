<x-modal title="Novo role">
    <form wire:submit="save" class="space-y-4">
        <x-input name="name" label="Nome" wire:model="name" autofocus placeholder="ex: financeiro" />

        <div class="flex justify-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar role</x-button>
        </div>
    </form>
</x-modal>
