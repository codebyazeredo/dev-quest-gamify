<x-modal title="Novo desafio" size="lg">
    <form wire:submit="save" class="vstack gap-3">
        @include('livewire.admin.challenges.partials.form-fields')

        <x-reward-title-fields :icons="$icons" :award="$award_title" source="desafio" />

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar desafio</x-button>
        </div>
    </form>
</x-modal>
