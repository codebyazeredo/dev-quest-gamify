@props(['color', 'selector' => 'html:root'])

@php
    $hex = preg_match('/^#[0-9a-f]{6}$/i', (string) $color) ? $color : \App\Models\AppSetting::DEFAULT_PRIMARY_COLOR;
    [$red, $green, $blue] = sscanf(ltrim($hex, '#'), '%02x%02x%02x');
    $linear = fn (int $channel): float => ($channel / 255) <= 0.03928 ? ($channel / 255) / 12.92 : ((($channel / 255) + 0.055) / 1.055) ** 2.4;
    $luminance = 0.2126 * $linear($red) + 0.7152 * $linear($green) + 0.0722 * $linear($blue);
    $darkTextLuminance = 0.017;
    $whiteContrast = 1.05 / ($luminance + 0.05);
    $darkContrast = ($luminance + 0.05) / ($darkTextLuminance + 0.05);
    $contrast = $whiteContrast >= $darkContrast ? '#ffffff' : '#212529';
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

    @if ($selector === 'html:root')
        html[data-bs-theme='dark']:root {
            --bs-primary-text-emphasis: color-mix(in srgb, {{ $hex }} 55%, white);
            --bs-primary-bg-subtle: color-mix(in srgb, {{ $hex }} 22%, #1d2125);
            --bs-primary-border-subtle: color-mix(in srgb, {{ $hex }} 45%, #1d2125);
            --bs-link-color: color-mix(in srgb, {{ $hex }} 65%, white);
            --bs-link-hover-color: color-mix(in srgb, {{ $hex }} 50%, white);
        }
    @endif
</style>
