@props(['icon', 'color' => 'primary', 'size' => 'md'])

@php
    $colorClasses = match ($color) {
        'gold' => 'bg-gold/10 text-gold',
        'forest' => 'bg-forest/10 text-forest',
        'terracotta' => 'bg-terracotta/10 text-terracotta',
        'amber-clay' => 'bg-amber-clay/10 text-amber-clay',
        'accent' => 'bg-accent/10 text-accent',
        default => 'bg-primary/10 text-primary',
    };
    $sizeClasses = match ($size) {
        'sm' => 'h-8 w-8',
        'lg' => 'h-12 w-12',
        default => 'h-10 w-10',
    };
    $iconSize = match ($size) {
        'sm' => 'h-4 w-4',
        'lg' => 'h-6 w-6',
        default => 'h-5 w-5',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex shrink-0 items-center justify-center rounded-full {$sizeClasses} {$colorClasses}"]) }}>
    <x-icon :name="$icon" :class="$iconSize" />
</span>
