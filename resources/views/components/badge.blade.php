@props(['color' => 'secondary'])

<span {{ $attributes->merge(['class' => "lozenge bg-{$color}-subtle text-{$color}-emphasis"]) }}>
    {{ $slot }}
</span>
