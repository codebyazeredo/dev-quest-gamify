<x-modal title="Editar título" size="lg">
    <form wire:submit="save" class="vstack gap-3">
        <x-input name="name" label="Nome" wire:model="name" autofocus />

        <x-textarea name="description" label="Descrição" wire:model="description" rows="2" />

        <div>
            <span class="form-label fw-medium small d-block mb-1">Ícone</span>
            <x-icon-picker model="icon" :icons="$icons" />
            @error('icon') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        @include('livewire.admin.titles.partials.unlock-rule')

        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="active-switch" wire:model="active">
            <label class="form-check-label" for="active-switch">Ativo</label>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Salvar alterações</x-button>
        </div>
    </form>
</x-modal>
