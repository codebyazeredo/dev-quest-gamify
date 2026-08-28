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

        @livewireStyles
    </head>
    <body class="min-h-screen text-ink lg:h-screen lg:overflow-hidden">
        <div class="flex min-h-screen flex-col lg:h-full lg:flex-row">
            <div class="flex shrink-0 flex-col items-center justify-center gap-3 bg-linear-to-r from-primary to-accent px-6 py-8 lg:order-2 lg:flex-1 lg:gap-8 lg:px-8 lg:py-10">
                @if ($appSettings->logoUrl())
                    @php
                        $logoSizeClasses = match ($appSettings->logo_size) {
                            \App\Enums\LogoSize::SMALL => 'lg:w-full lg:max-w-[45%] lg:max-h-[30vh]',
                            \App\Enums\LogoSize::MEDIUM => 'lg:w-full lg:max-w-[65%] lg:max-h-[45vh]',
                            \App\Enums\LogoSize::LARGE => 'lg:w-full lg:max-w-[85%] lg:max-h-[60vh]',
                        };
                    @endphp
                    <img src="{{ $appSettings->logoUrl() }}" alt="{{ $appSettings->displayName() }}" class="h-16 w-auto max-w-full lg:h-auto {{ $logoSizeClasses }}">
                @else
                    <span class="text-xl font-bold tracking-tight text-white lg:text-4xl">{{ $appSettings->displayName() }}</span>
                @endif

                <p class="hidden max-w-md text-center text-xl font-medium text-white/90 lg:block">
                    Transforme tarefas em conquistas: gamificação de verdade para o seu time de desenvolvimento.
                </p>
            </div>

            <div class="flex flex-1 flex-col justify-center bg-card px-6 py-10 lg:order-1 lg:flex-none lg:w-[42%] lg:overflow-y-auto lg:px-16">
                <div class="mx-auto w-full max-w-sm">
                    @if (session('status'))
                        <div class="mb-4 rounded-xl border border-forest/30 bg-forest/10 p-4 text-sm text-forest">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>

                <x-footer class="mx-auto mt-10 w-full max-w-sm" />
            </div>
        </div>

        @livewireScripts
    </body>
</html>
