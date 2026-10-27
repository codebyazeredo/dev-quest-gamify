@props(['placeholder' => 'Buscar'])

<div class="d-flex flex-wrap gap-2 mb-3">
    <div class="input-group" style="max-width: 18rem;">
        <span class="input-group-text bg-body-tertiary"><i class="bi bi-search" aria-hidden="true"></i></span>
        <input type="search" x-model.debounce.300ms="filters.search" x-on:input.debounce.300ms="applyFilters" class="form-control" placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}">
    </div>

    {{ $slot }}
</div>
