@props(['name', 'class' => ''])

@php
    $bootstrapIcons = [
        'star' => 'star-fill',
        'trophy' => 'trophy-fill',
        'flag' => 'flag-fill',
        'fire' => 'fire',
        'bug' => 'bug-fill',
        'rocket' => 'rocket-takeoff-fill',
        'bolt' => 'lightning-charge-fill',
        'check' => 'check-lg',
        'alert' => 'exclamation-triangle-fill',
        'medal' => 'award-fill',
        'pencil' => 'pencil-square',
        'trash' => 'trash3',
        'chevron-down' => 'chevron-down',
        'close' => 'x-lg',
        'chevrons-left' => 'chevron-double-left',
        'chevrons-right' => 'chevron-double-right',
        'home' => 'house-door',
        'columns' => 'kanban',
        'users' => 'people',
        'id-card' => 'person-vcard',
        'shield' => 'shield-check',
        'tag' => 'tag',
        'archive' => 'archive',
        'gear' => 'gear',
        'mail' => 'envelope',
        'lock' => 'lock',
        'eye' => 'eye',
        'eye-off' => 'eye-slash',
        'plus' => 'plus-lg',
    ];
@endphp

<i {{ $attributes->merge(['class' => 'bi bi-'.($bootstrapIcons[$name] ?? 'question-circle').' '.$class]) }} aria-hidden="true"></i>
