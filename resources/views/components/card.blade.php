@props(['variant' => 'default', 'padding' => 'p-3'])

@php
    $variantClasses = match ($variant) {
        'highlight' => 'border-warning bg-warning-subtle',
        'active' => 'border-primary bg-primary-subtle',
        'alert' => 'border-danger-subtle bg-danger-subtle text-danger-emphasis',
        default => '',
    };
@endphp

<div {{ $attributes->merge(['class' => "card {$variantClasses}"]) }}>
    <div class="card-body {{ $padding }}">
        {{ $slot }}
    </div>
</div>
