@props(['icon', 'label', 'href' => null, 'variant' => 'outline-secondary'])

@php
    $classes = "btn btn-{$variant} btn-icon".($variant === 'outline-secondary' ? ' bg-body' : '');
    $tooltip = ['data-bs-toggle' => 'tooltip', 'data-bs-title' => $label, 'aria-label' => $label];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, ...$tooltip]) }}>
        <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => $classes, ...$tooltip]) }}>
        <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
    </button>
@endif
