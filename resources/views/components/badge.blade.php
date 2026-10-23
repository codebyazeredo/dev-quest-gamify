@props(['color' => 'secondary'])

<span {{ $attributes->merge(['class' => "badge rounded-pill d-inline-flex align-items-center gap-1 fw-medium bg-{$color}-subtle text-{$color}-emphasis"]) }}>
    {{ $slot }}
</span>
