@props(['model', 'icons'])

<div class="d-flex flex-wrap gap-2">
    @foreach ($icons as $iconName)
        <input type="radio" class="btn-check" id="{{ $model }}-icon-{{ $iconName }}" wire:model="{{ $model }}" value="{{ $iconName }}" autocomplete="off">
        <label class="btn btn-outline-secondary btn-icon fs-5" for="{{ $model }}-icon-{{ $iconName }}" title="{{ $iconName }}">
            <x-icon :name="$iconName" />
        </label>
    @endforeach
</div>
