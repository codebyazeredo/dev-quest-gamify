<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ?? config('app.name', 'DevQuestGamify') }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="flex min-h-screen flex-col items-center justify-center bg-gray-100 dark:bg-gray-900">
        <div class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">
            {{ config('app.name', 'DevQuestGamify') }}
        </div>

        @if (session('status'))
            <div class="mb-4 w-full max-w-md rounded-md bg-green-50 p-4 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="w-full max-w-md rounded-lg bg-white p-8 shadow dark:bg-gray-800">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
