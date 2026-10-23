<x-modal title="Nova categoria">
    <form wire:submit="save" class="vstack gap-3">
        <x-input name="name" label="Nome" wire:model="name" autofocus />

        <x-input name="base_points" label="Pontos base" type="number" min="0" wire:model="base_points" />

        <div>
            <span class="form-label fw-medium small d-block mb-0">Cor da categoria</span>
            <div class="form-text mt-0 mb-2">Usada no card da tarefa no Kanban — a cor da fonte já vem ajustada para ficar legível.</div>

            <x-color-picker :pairs="$colorPairs" :selected="$color" />
            @error('color') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

            <div class="rounded-3 border p-3 mt-3 fw-medium" style="background-color: {{ $color }}; color: {{ $text_color }};">
                Pré-visualização: {{ $name !== '' ? $name : 'Nome da categoria' }}
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar categoria</x-button>
        </div>
    </form>
</x-modal>
