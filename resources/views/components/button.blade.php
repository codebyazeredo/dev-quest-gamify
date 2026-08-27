@props(['variant' => 'primary'])

@php
    $variantClasses = match ($variant) {
        'secondary' => 'font-medium text-ink-muted hover:bg-line/20',
        'danger' => 'bg-terracotta font-semibold text-white hover:opacity-90',
        default => 'bg-primary font-semibold text-white hover:bg-primary-hover',
    };
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => "rounded-lg px-4 py-2 text-sm {$variantClasses}"]) }}>
    {{ $slot }}
</button>
