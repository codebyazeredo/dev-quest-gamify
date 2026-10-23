<x-modal title="Novo quadro">
    <form wire:submit="save" class="vstack gap-3">
        <x-input name="name" label="Nome" wire:model="name" required autofocus />

        <x-textarea name="description" label="Descrição" wire:model="description" />

        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="board-create-active" wire:model="is_active">
            <label class="form-check-label" for="board-create-active">Ativo</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="board-create-columns" wire:model="seedDefaultColumns">
            <label class="form-check-label" for="board-create-columns">
                Criar colunas padrão de desenvolvimento
                <span class="d-block small text-body-secondary">Backlog, A Fazer, Em Andamento, Em Revisão, Em Teste, Aprovado, Concluído</span>
            </label>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar quadro</x-button>
        </div>
    </form>
</x-modal>
