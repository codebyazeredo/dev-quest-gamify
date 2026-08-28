<x-modal title="Novo quadro">
    <form wire:submit="save" class="space-y-4">
        <x-input name="name" label="Nome" wire:model="name" required autofocus />

        <x-textarea name="description" label="Descrição" wire:model="description" />

        <label class="flex items-center gap-2 text-sm text-ink-muted">
            <input type="checkbox" wire:model="is_active" class="rounded border-line">
            Ativo
        </label>

        <label class="flex items-center gap-2 text-sm text-ink-muted">
            <input type="checkbox" wire:model="seedDefaultColumns" class="rounded border-line">
            Criar colunas padrão de desenvolvimento (Backlog, A Fazer, Em Andamento, Em Revisão, Em Teste, Aprovado, Concluído)
        </label>

        <div class="flex justify-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar quadro</x-button>
        </div>
    </form>
</x-modal>
