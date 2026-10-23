@props(['color', 'selector' => 'html:root'])

@php
    $hex = preg_match('/^#[0-9a-f]{6}$/i', (string) $color) ? $color : \App\Models\AppSetting::DEFAULT_PRIMARY_COLOR;
    [$red, $green, $blue] = sscanf(ltrim($hex, '#'), '%02x%02x%02x');
    $isLight = ($red * 299 + $green * 587 + $blue * 114) / 1000 > 165;
    $contrast = $isLight ? '#212529' : '#ffffff';
@endphp

<style>
    {{ $selector }} {
        --bs-primary: {{ $hex }};
        --bs-primary-rgb: {{ $red }}, {{ $green }}, {{ $blue }};
        --app-primary-contrast: {{ $contrast }};
        --bs-primary-text-emphasis: color-mix(in srgb, {{ $hex }} 55%, black);
        --bs-primary-bg-subtle: color-mix(in srgb, {{ $hex }} 12%, white);
        --bs-primary-border-subtle: color-mix(in srgb, {{ $hex }} 35%, white);
        --bs-link-color: {{ $hex }};
        --bs-link-color-rgb: {{ $red }}, {{ $green }}, {{ $blue }};
        --bs-link-hover-color: color-mix(in srgb, {{ $hex }} 80%, black);
        --bs-link-hover-color-rgb: {{ $red }}, {{ $green }}, {{ $blue }};
    }
</style>
