{{-- Rodapé de paginação para telas que usam o componente Alpine "crudTable". --}}
@props(['options' => [10, 25, 50]])

<div class="card-footer bg-body-tertiary d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-2 small text-body-secondary">
        <label for="perPage" class="mb-0">Itens por página</label>
        <select x-model.number="perPage" x-on:change="changePerPage" id="perPage" class="form-select form-select-sm w-auto">
            @foreach ($options as $option)
                <option value="{{ $option }}">{{ $option }}</option>
            @endforeach
        </select>
    </div>

    <div class="d-flex align-items-center gap-2 small text-body-secondary">
        <span x-text="`Página ${meta.current_page} de ${meta.last_page}`"></span>
        <button type="button" class="btn btn-outline-secondary btn-sm" x-on:click="load(meta.current_page - 1)" x-bind:disabled="meta.current_page <= 1" aria-label="Página anterior"><i class="bi bi-chevron-left" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-outline-secondary btn-sm" x-on:click="load(meta.current_page + 1)" x-bind:disabled="meta.current_page >= meta.last_page" aria-label="Próxima página"><i class="bi bi-chevron-right" aria-hidden="true"></i></button>
    </div>
</div>
