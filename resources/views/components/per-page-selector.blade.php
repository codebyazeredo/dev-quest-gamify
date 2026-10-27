@props(['options' => [10, 25, 50]])

@php
    $currentPerPage = (int) request('porPagina', $options[0]);
@endphp

<div class="d-flex align-items-center gap-2 small text-body-secondary">
    <label for="perPage" class="mb-0">Itens por página</label>
    <select
        id="perPage"
        class="form-select form-select-sm w-auto"
        x-data
        x-on:change="const url = new URL(window.location); url.searchParams.set('porPagina', $event.target.value); url.searchParams.delete('page'); window.location = url"
    >
        @foreach ($options as $option)
            <option value="{{ $option }}" @selected($currentPerPage === $option)>{{ $option }}</option>
        @endforeach
    </select>
</div>
