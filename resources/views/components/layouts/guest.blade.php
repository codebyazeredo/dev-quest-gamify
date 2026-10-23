<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php $appSettings = \App\Models\AppSetting::current(); @endphp

        <title>{{ $title ?? $appSettings->displayName() }}</title>

        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/x-icon" href="/favicon.ico" sizes="any">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <x-theme-style :color="$appSettings->primaryColor()" />

        @livewireStyles
    </head>
    <body class="auth-page d-flex flex-column align-items-center justify-content-center px-3 py-5">
        <div class="auth-card p-4 p-sm-5">
            <div class="text-center mb-4">
                @if ($appSettings->logoUrl())
                    @php
                        $logoHeight = match ($appSettings->logo_size) {
                            \App\Enums\LogoSize::SMALL => '3rem',
                            \App\Enums\LogoSize::MEDIUM => '5rem',
                            \App\Enums\LogoSize::LARGE => '7rem',
                        };
                    @endphp
                    <img src="{{ $appSettings->logoUrl() }}" alt="{{ $appSettings->displayName() }}" style="height: {{ $logoHeight }}; max-width: 100%; width: auto;">
                @else
                    <span class="brand-tile mb-2" style="width: 3.5rem; height: 3.5rem;"><i class="bi bi-controller fs-3 text-primary" aria-hidden="true"></i></span>
                    <div class="fw-semibold fs-5">{{ $appSettings->displayName() }}</div>
                @endif
            </div>

            @if (session('status'))
                <div class="alert alert-success d-flex align-items-start gap-2" role="alert">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            {{ $slot }}
        </div>

        <x-footer class="text-center mt-4" />

        @livewireScripts
    </body>
</html>
