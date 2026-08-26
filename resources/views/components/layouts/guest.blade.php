<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php $appSettings = \App\Models\AppSetting::current(); @endphp

        <title>{{ $title ?? $appSettings->company_name ?: config('app.name', 'Dev Quest') }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="flex min-h-screen flex-col items-center justify-center bg-surface px-4 text-ink">
        <div class="mb-6 flex items-center gap-2 text-xl font-bold tracking-tight text-ink">
            @if ($appSettings->logo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($appSettings->logo_path) }}" alt="" class="h-8 w-auto">
            @endif
            {{ $appSettings->company_name ?: config('app.name', 'Dev Quest') }}
        </div>

        @if (session('status'))
            <div class="mb-4 w-full max-w-md rounded-xl border border-forest/30 bg-forest/10 p-4 text-sm text-forest">
                {{ session('status') }}
            </div>
        @endif

        <div class="w-full max-w-md rounded-2xl border border-line bg-card p-8 shadow-sm">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
