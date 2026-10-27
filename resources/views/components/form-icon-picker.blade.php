@props(['name' => 'icon', 'icons', 'label' => 'Ícone'])

<div>
    <span class="form-label fw-medium small d-block mb-1">{{ $label }}</span>

    <div class="d-flex flex-wrap gap-2">
        @foreach ($icons as $iconName)
            <input type="radio" class="btn-check" id="{{ $name }}-icon-{{ $iconName }}" x-model="form.{{ $name }}" value="{{ $iconName }}" autocomplete="off">
            <label class="btn btn-outline-secondary btn-icon fs-5" for="{{ $name }}-icon-{{ $iconName }}" title="{{ $iconName }}">
                <x-icon :name="$iconName" />
            </label>
        @endforeach
    </div>

    <div class="text-danger small mt-1" x-text="firstError('{{ $name }}')"></div>
</div>
