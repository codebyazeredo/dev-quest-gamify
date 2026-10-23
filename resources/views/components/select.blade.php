@props(['label' => null, 'name', 'placeholder' => null])

@php
    $id = $attributes->get('id') ?: $name;
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="form-label fw-medium small mb-1">{{ $label }}</label>
    @endif

    <select
        id="{{ $id }}"
        {{ $attributes->whereDoesntStartWith('id')->class(['form-select', 'is-invalid' => $errors->has($name)]) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>

    @error($name) <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
