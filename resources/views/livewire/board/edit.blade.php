<x-modal title="Editar quadro" size="lg">
    <form wire:submit="save" class="vstack gap-3">
        <x-input id="edit-name" name="name" label="Nome" wire:model="name" required />

        <x-textarea id="edit-description" name="description" label="Descrição" wire:model="description" rows="2" />

        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="board-edit-active" wire:model="is_active">
            <label class="form-check-label" for="board-edit-active">Ativo</label>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Fechar</x-button>
            <x-button type="submit">Salvar</x-button>
        </div>
    </form>
</x-modal>
