@props(['name', 'label'])

<div>
    <label for="{{ $name }}" class="form-label fw-medium small mb-1">{{ $label }}</label>

    <textarea
        id="{{ $name }}"
        x-model="form.{{ $name }}"
        x-bind:class="{ 'is-invalid': firstError('{{ $name }}') }"
        {{ $attributes->class(['form-control']) }}
    ></textarea>

    <div class="invalid-feedback" x-text="firstError('{{ $name }}')"></div>
</div>
