<x-modal title="Novo título">
    <form wire:submit="save" class="vstack gap-3">
        <x-input name="name" label="Nome" wire:model="name" autofocus />

        <div>
            <label class="form-label fw-medium small mb-1">Ícone</label>
            <x-icon-picker model="icon" :icons="$icons" />
            @error('icon') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <x-select name="achievement_id" label="Conquista" wire:model="achievement_id" placeholder="Nenhuma">
            @foreach ($achievements as $achievement)
                <option value="{{ $achievement->id }}">{{ $achievement->name }}</option>
            @endforeach
        </x-select>

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar título</x-button>
        </div>
    </form>
</x-modal>
