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

    @case('close')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M6 6l12 12M18 6l-12 12" />
        </svg>
        @break

    @case('chevrons-left')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M13 6l-6 6 6 6" />
            <path d="M19 6l-6 6 6 6" />
        </svg>
        @break

    @case('chevrons-right')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M11 6l6 6-6 6" />
            <path d="M5 6l6 6-6 6" />
        </svg>
        @break

    @case('home')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M4 11l8-7 8 7" />
            <path d="M6 9.5V20h12V9.5" />
            <path d="M10 20v-6h4v6" />
        </svg>
        @break

    @case('columns')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <rect x="3.5" y="4" width="17" height="16" rx="2" />
            <path d="M9 4v16M15 4v16" />
        </svg>
        @break

    @case('users')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <circle cx="9" cy="8" r="3" />
            <path d="M3.5 19c0-3 2.5-5 5.5-5s5.5 2 5.5 5" />
            <path d="M16 4.5c1.7.3 3 1.8 3 3.5s-1.3 3.2-3 3.5" />
            <path d="M15.5 14c2.5.4 4.5 2.3 4.5 5" />
        </svg>
        @break

    @case('id-card')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <rect x="3" y="5" width="18" height="14" rx="2" />
            <circle cx="8.5" cy="11" r="2" />
            <path d="M5.5 16c.5-1.7 1.7-2.5 3-2.5s2.5.8 3 2.5" />
            <path d="M14 10h6M14 13.5h6" />
        </svg>
        @break

    @case('shield')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M12 3l7 3v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3z" />
            <path d="M9 12l2 2 4-4" />
        </svg>
        @break

    @case('tag')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M12.5 3.5H5A1.5 1.5 0 003.5 5v7.5a1.5 1.5 0 00.44 1.06l8.5 8.5a1.5 1.5 0 002.12 0l7.44-7.44a1.5 1.5 0 000-2.12l-8.5-8.5a1.5 1.5 0 00-1.06-.44z" />
            <circle cx="8" cy="8" r="1.5" />
        </svg>
        @break

    @case('gear')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <circle cx="12" cy="12" r="3" />
            <path d="M19.4 13.5a1.7 1.7 0 00.34 1.87l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.7 1.7 0 00-1.87-.34 1.7 1.7 0 00-1.04 1.56V19.6a2 2 0 11-4 0v-.09a1.7 1.7 0 00-1.04-1.56 1.7 1.7 0 00-1.87.34l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.7 1.7 0 00.34-1.87 1.7 1.7 0 00-1.56-1.04H4.4a2 2 0 110-4h.09a1.7 1.7 0 001.56-1.04 1.7 1.7 0 00-.34-1.87l-.06-.06a2 2 0 112.83-2.83l.06.06a1.7 1.7 0 001.87.34H10.5a1.7 1.7 0 001.04-1.56V4.4a2 2 0 114 0v.09a1.7 1.7 0 001.04 1.56 1.7 1.7 0 001.87-.34l.06-.06a2 2 0 112.83 2.83l-.06.06a1.7 1.7 0 00-.34 1.87V10.5a1.7 1.7 0 001.56 1.04h.09a2 2 0 110 4h-.09a1.7 1.7 0 00-1.56 1.04z" />
        </svg>
        @break
@endswitch
