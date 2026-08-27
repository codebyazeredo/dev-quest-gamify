<x-modal title="Editar categoria">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-ink">Nome</label>
            <input type="text" wire:model="name" autofocus
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            @error('name') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-ink">Pontos base</label>
            <input type="number" min="0" wire:model="base_points"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            @error('base_points') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-ink">Cor da categoria</label>
            <p class="mt-0.5 text-xs text-ink-muted">Usada no card da tarefa no Kanban — a cor da fonte já vem ajustada para ficar legível.</p>
            <div class="mt-2">
                <x-color-picker :pairs="$colorPairs" :selected="$color" />
            </div>
            @error('color') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror

            <div class="mt-3 flex items-center gap-2 rounded-lg border border-line p-3" style="background-color: {{ $color }}; color: {{ $text_color }};">
                <span class="text-sm font-medium">Pré-visualização: {{ $name !== '' ? $name : 'Nome da categoria' }}</span>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-line/20">
                Cancelar
            </button>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
                Salvar alterações
            </button>
        </div>
    </form>
</x-modal>
