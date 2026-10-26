{{-- Campo ligado ao `form` e aos `errors` do componente Alpine "crudTable". --}}
@props(['name', 'label', 'type' => 'text', 'hint' => null])

<div>
    <label for="{{ $name }}" class="form-label fw-medium small mb-1">{{ $label }}</label>

    <input
        id="{{ $name }}"
        type="{{ $type }}"
        @if ($type === 'number') x-model.number="form.{{ $name }}" @else x-model="form.{{ $name }}" @endif
        x-bind:class="{ 'is-invalid': firstError('{{ $name }}') }"
        {{ $attributes->class(['form-control']) }}
    >

    <div class="invalid-feedback" x-text="firstError('{{ $name }}')"></div>

    @if ($hint)
        <div class="form-text">{{ $hint }}</div>
    @endif
</div>
