@props(['icon', 'label', 'href' => null, 'variant' => 'default'])

@php
    $variantClass = match ($variant) {
        'primary' => 'btn-ghost-primary',
        'danger' => 'btn-ghost-danger',
        default => '',
    };
    $classes = trim("btn btn-ghost btn-icon {$variantClass}");
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
