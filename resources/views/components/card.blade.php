@props(['variant' => 'default', 'padding' => 'p-4', 'rounded' => 'rounded-xl'])

@php
    $variantClasses = match ($variant) {
        'highlight' => 'border-2 border-gold/40 bg-gold/5 shadow-md shadow-gold/10 ring-1 ring-gold/20',
        'active' => 'border-primary/40 bg-primary/5',
        'alert' => 'border-terracotta/30 bg-terracotta/10 text-terracotta',
        default => 'border-line/60 bg-card shadow-sm',
    };
@endphp

<div {{ $attributes->merge(['class' => "{$rounded} border {$padding} {$variantClasses}"]) }}>
    {{ $slot }}
</div>
