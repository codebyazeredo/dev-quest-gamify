@props(['name', 'label'])

<div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" role="switch" id="{{ $name }}" x-model="form.{{ $name }}">
    <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
</div>
