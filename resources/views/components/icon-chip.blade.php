@props(['icon', 'color' => 'primary', 'size' => 'md'])

@php
    $sizeClass = match ($size) {
        'sm' => 'icon-chip-sm',
        'lg' => 'icon-chip-lg',
        default => '',
    };
@endphp

<span {{ $attributes->merge(['class' => "icon-chip {$sizeClass} bg-{$color}-subtle text-{$color}-emphasis"]) }}>
    <x-icon :name="$icon" />
</span>
