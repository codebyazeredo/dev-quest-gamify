@props(['variant' => 'primary'])

@php
    $variantClass = match ($variant) {
        'secondary' => 'btn-outline-secondary',
        'danger' => 'btn-danger',
        'success' => 'btn-success',
        default => 'btn-primary',
    };
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => "btn {$variantClass}"]) }}>
    {{ $slot }}
</button>
