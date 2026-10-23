@props(['options' => [10, 25, 50]])

<div class="d-flex align-items-center gap-2 small text-body-secondary">
    <label for="perPage" class="mb-0">Itens por página</label>
    <select wire:model.live="perPage" id="perPage" class="form-select form-select-sm w-auto">
        @foreach ($options as $option)
            <option value="{{ $option }}">{{ $option }}</option>
        @endforeach
    </select>
</div>
