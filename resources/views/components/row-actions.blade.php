@props(['edit' => null, 'delete' => null, 'confirm' => 'Excluir este registro?'])

<div class="d-inline-flex gap-1">
    @if ($edit)
        <button type="button" wire:click="{{ $edit }}" title="Editar" aria-label="Editar" class="btn btn-outline-secondary btn-icon btn-icon-sm">
            <x-icon name="pencil" />
        </button>
    @endif

    @if ($delete)
        <button type="button" wire:click="{{ $delete }}" wire:confirm="{{ $confirm }}" title="Excluir" aria-label="Excluir" class="btn btn-outline-danger btn-icon btn-icon-sm">
            <x-icon name="trash" />
        </button>
    @endif
</div>
