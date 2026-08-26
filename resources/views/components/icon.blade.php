@props(['name', 'class' => 'h-5 w-5'])

@switch($name)
    @case('star')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M12 3.5l2.6 5.27 5.82.85-4.21 4.1.99 5.79L12 16.9l-5.2 2.61.99-5.79-4.21-4.1 5.82-.85L12 3.5z" />
        </svg>
        @break

    @case('trophy')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M8 4h8v4a4 4 0 01-8 0V4z" />
            <path d="M8 5H5a3 3 0 003 3M16 5h3a3 3 0 01-3 3" />
            <path d="M10 15h4v3h-4z" />
            <path d="M8 20h8" />
        </svg>
        @break

    @case('flag')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M5 3v18" />
            <path d="M5 4h13l-3 4 3 4H5" />
        </svg>
        @break

    @case('fire')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M12 3c1 2.5-2 4-2 7a3 3 0 006 0c1 1 1.5 2.5 1.5 4a5.5 5.5 0 11-11 0C6.5 9 9 7 12 3z" />
        </svg>
        @break

    @case('bug')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <rect x="7" y="8" width="10" height="10" rx="5" />
            <path d="M9 8V6a3 3 0 016 0v2" />
            <path d="M4 11h3M17 11h3M4 15h3M17 15h3M9 4l1.5 2M15 4l-1.5 2" />
        </svg>
        @break

    @case('rocket')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M12 2c2.5 1.5 4 4.5 4 8.5 0 2-1 4-2 5.5l-2 2-2-2c-1-1.5-2-3.5-2-5.5C8 6.5 9.5 3.5 12 2z" />
            <path d="M9 15l-2.5 1.5L7 13M15 15l2.5 1.5L17 13" />
            <circle cx="12" cy="9" r="1.2" />
        </svg>
        @break

    @case('bolt')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M13 2L4 14h6l-1 8 9-12h-6l1-8z" />
        </svg>
        @break

    @case('check')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M5 12.5l4.5 4.5L19 7" />
        </svg>
        @break

    @case('alert')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <circle cx="12" cy="12" r="9" />
            <path d="M12 8v5" />
            <path d="M12 16h.01" />
        </svg>
        @break

    @case('medal')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <circle cx="12" cy="15" r="6" />
            <path d="M9 4l-3 6.5M15 4l3 6.5" />
            <path d="M10.5 13.5l1.5-1 1.5 1-.5 1.8h-2z" />
        </svg>
        @break

    @case('pencil')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M16.5 3.5a2.12 2.12 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
        </svg>
        @break

    @case('trash')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M4 7h16" />
            <path d="M9 7V4h6v3" />
            <path d="M6 7l1 13h10l1-13" />
            <path d="M10 11v6M14 11v6" />
        </svg>
        @break

    @case('chevron-down')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M6 9l6 6 6-6" />
        </svg>
        @break
@endswitch
