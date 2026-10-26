{{-- Botões de editar/excluir de uma linha do "crudTable" (variável Alpine `row`). --}}
@props(['confirm' => 'Excluir este registro?', 'edit' => true, 'delete' => true])

<div class="d-inline-flex gap-1">
    @if ($edit)
        <button type="button" x-on:click="openEdit(row)" title="Editar" aria-label="Editar" class="btn btn-ghost btn-icon btn-icon-sm"><x-icon name="pencil" /></button>
    @endif

    @if ($delete)
        <button type="button" x-on:click="remove(row, '{{ $confirm }}')" title="Excluir" aria-label="Excluir" class="btn btn-ghost btn-ghost-danger btn-icon btn-icon-sm"><x-icon name="trash" /></button>
    @endif
</div>
