@props(['name', 'class' => ''])

@php
    $bootstrapIcons = \App\Support\FlavorIcons::bootstrapNames();
@endphp

<i {{ $attributes->merge(['class' => 'bi bi-'.($bootstrapIcons[$name] ?? 'question-circle').' '.$class]) }} aria-hidden="true"></i>
