@props(['color' => 'neutral'])

@php
    $colorClasses = match ($color) {
        'gold' => 'bg-gold/10 text-gold',
        'forest' => 'bg-forest/10 text-forest',
        'terracotta' => 'bg-terracotta/10 text-terracotta',
        'amber-clay' => 'bg-amber-clay/10 text-amber-clay',
        'accent' => 'bg-accent/10 text-accent',
        'primary' => 'bg-primary/10 text-primary',
        default => 'bg-line/40 text-ink-muted',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium {$colorClasses}"]) }}>
    {{ $slot }}
</span>
