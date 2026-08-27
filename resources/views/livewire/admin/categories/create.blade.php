<x-modal title="Nova categoria">
    <form wire:submit="save" class="space-y-4">
        <x-input name="name" label="Nome" wire:model="name" autofocus />

        <x-input name="base_points" label="Pontos base" type="number" min="0" wire:model="base_points" />

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
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar categoria</x-button>
        </div>
    </form>
</x-modal>
