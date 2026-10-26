<x-modal title="Editar conquista" size="lg">
    <form wire:submit="save" class="vstack gap-3">
        @include('livewire.admin.achievements.partials.form-fields')

        <x-reward-title-fields :icons="$icons" :award="$award_title" :existing="$rewardTitles" source="conquista" />

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
