@props(['label' => null, 'name', 'rows' => 3])

@php
    $id = $attributes->get('id') ?: $name;
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="form-label fw-medium small mb-1">{{ $label }}</label>
    @endif

    <textarea
        id="{{ $id }}"
        rows="{{ $rows }}"
        {{ $attributes->whereDoesntStartWith(['id', 'rows'])->class(['form-control', 'is-invalid' => $errors->has($name)]) }}
    ></textarea>

    @error($name) <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
