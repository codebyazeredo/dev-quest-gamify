<x-modal title="Editar quadro" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="edit-name" class="block text-sm font-medium text-ink">Nome</label>
            <input id="edit-name" type="text" wire:model="name" required
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            @error('name') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="edit-description" class="block text-sm font-medium text-ink">Descrição</label>
            <textarea id="edit-description" wire:model="description" rows="2"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30"></textarea>
        </div>

        <label class="flex items-center gap-2 text-sm text-ink-muted">
            <input type="checkbox" wire:model="is_active" class="rounded border-line">
            Ativo
        </label>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-line/20">
                Fechar
            </button>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
                Salvar
            </button>
        </div>
    </form>

    <hr class="my-6 border-line">

    <h3 class="mb-3 text-sm font-semibold text-ink">Colunas</h3>

    @error('columns') <p class="mb-2 text-sm text-terracotta">{{ $message }}</p> @enderror

    <div class="space-y-2">
        @foreach ($board->columns as $column)
            <div class="flex flex-wrap items-center gap-2 rounded-lg border border-line p-2" wire:key="column-{{ $column->id }}">
                <input type="text" value="{{ $column->name }}"
                    wire:change="renameColumn({{ $column->id }}, $event.target.value)"
                    class="min-w-0 flex-1 basis-32 rounded-lg border border-line bg-card px-2 py-1 text-sm text-ink">

                <select wire:change="setColumnStatus({{ $column->id }}, $event.target.value)" class="min-w-0 shrink-0 rounded-lg border border-line bg-card px-2 py-1 text-sm text-ink">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected($column->status === $status)>{{ $status->label() }}</option>
                    @endforeach
                </select>

                <div class="flex shrink-0 gap-1">
                    <button type="button" wire:click="moveColumnUp({{ $column->id }})" class="rounded px-2 py-1 text-ink-muted hover:bg-line/20">&uarr;</button>
                    <button type="button" wire:click="moveColumnDown({{ $column->id }})" class="rounded px-2 py-1 text-ink-muted hover:bg-line/20">&darr;</button>
                    <button type="button" wire:click="deleteColumn({{ $column->id }})" wire:confirm="Excluir esta coluna?" class="rounded px-2 py-1 text-terracotta hover:bg-terracotta/10">&times;</button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-2">
        <input type="text" wire:model="newColumnName" placeholder="Nome da nova coluna"
            class="min-w-0 flex-1 basis-32 rounded-lg border border-line bg-card px-2 py-1 text-sm text-ink">

        <select wire:model="newColumnStatus" class="min-w-0 shrink-0 rounded-lg border border-line bg-card px-2 py-1 text-sm text-ink">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}">{{ $status->label() }}</option>
            @endforeach
        </select>

        <button type="button" wire:click="addColumn" class="shrink-0 rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-hover">
            Adicionar
        </button>
    </div>
</x-modal>
