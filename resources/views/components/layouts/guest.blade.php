<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php $appSettings = \App\Models\AppSetting::current(); @endphp

        <title>{{ $title ?? $appSettings->displayName() }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="flex min-h-screen flex-col items-center justify-center bg-surface px-4 text-ink">
        @if (session('status'))
            <div class="mb-4 w-full max-w-md rounded-xl border border-forest/30 bg-forest/10 p-4 text-sm text-forest">
                {{ session('status') }}
            </div>
        @endif

        <div class="w-full max-w-md rounded-2xl border border-line bg-card p-8 shadow-sm">
            <div class="mb-6 flex items-center justify-center">
                @if ($appSettings->logoUrl())
                    <img src="{{ $appSettings->logoUrl() }}" alt="{{ $appSettings->displayName() }}" class="h-28 max-h-[22vh] w-auto max-w-full">
                @else
                    <span class="text-2xl font-bold tracking-tight text-ink">{{ $appSettings->displayName() }}</span>
                @endif
            </div>

            {{ $slot }}
        </div>

        <x-footer class="mt-6 w-full max-w-md" />

        @livewireScripts
    </body>
</html>
