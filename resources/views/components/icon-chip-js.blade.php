{{-- Versão Alpine do icon-chip: `icon` é uma expressão JS com o nome do ícone Bootstrap (ex.: "row.icon"). --}}
@props(['icon', 'color' => 'primary', 'size' => 'md'])

@php
    $sizeClass = match ($size) {
        'sm' => 'icon-chip-sm',
        'lg' => 'icon-chip-lg',
        default => '',
    };
@endphp

<span {{ $attributes->merge(['class' => "icon-chip {$sizeClass} bg-{$color}-subtle text-{$color}-emphasis"]) }}>
    <i class="bi" x-bind:class="`bi-${window.flavorIcons[{{ $icon }}] ?? 'question-circle'}`" aria-hidden="true"></i>
</span>
