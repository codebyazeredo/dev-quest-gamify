<x-modal title="Editar quadro" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <x-input id="edit-name" name="name" label="Nome" wire:model="name" required />

        <x-textarea id="edit-description" name="description" label="Descrição" wire:model="description" rows="2" />

        <label class="flex items-center gap-2 text-sm text-ink-muted">
            <input type="checkbox" wire:model="is_active" class="rounded border-line">
            Ativo
        </label>

        <div class="flex justify-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Fechar</x-button>
            <x-button type="submit">Salvar</x-button>
        </div>
    </form>
</x-modal>
