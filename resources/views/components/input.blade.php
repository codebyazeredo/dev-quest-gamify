@props(['label' => null, 'name', 'type' => 'text'])

@php
    $id = $attributes->get('id') ?: $name;
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="form-label fw-medium small mb-1">{{ $label }}</label>
    @endif

    <input
        id="{{ $id }}"
        type="{{ $type }}"
        {{ $attributes->whereDoesntStartWith('id')->class(['form-control', 'is-invalid' => $errors->has($name)]) }}
    >

    @error($name) <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
