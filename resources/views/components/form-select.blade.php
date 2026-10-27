@props(['name', 'label', 'placeholder' => null])

<div>
    <label for="{{ $name }}" class="form-label fw-medium small mb-1">{{ $label }}</label>

    <select
        id="{{ $name }}"
        x-model="form.{{ $name }}"
        x-bind:class="{ 'is-invalid': firstError('{{ $name }}') }"
        {{ $attributes->class(['form-select']) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>

    <div class="invalid-feedback" x-text="firstError('{{ $name }}')"></div>
</div>
