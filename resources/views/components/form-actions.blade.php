{{-- Rodapé padrão dos modais do "crudTable": cancelar e salvar. --}}
@props(['createLabel', 'updateLabel' => 'Salvar alterações'])

<div class="d-flex justify-content-end gap-2">
    <x-button variant="secondary" x-on:click="closeModal">Cancelar</x-button>
    <x-button type="submit" x-text="editingRow ? '{{ $updateLabel }}' : '{{ $createLabel }}'"></x-button>
</div>
