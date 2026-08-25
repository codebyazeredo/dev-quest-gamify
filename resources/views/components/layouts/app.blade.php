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
    <body class="bg-gray-100 dark:bg-gray-900">
        <div class="flex min-h-screen flex-col">
            <nav class="flex items-center justify-between border-b bg-white px-6 py-3 dark:border-gray-700 dark:bg-gray-800">
                <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                    {{ config('app.name', 'DevQuestGamify') }}
                </a>

                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 rounded-md px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-xs font-semibold text-white">
                            {{ auth()->user()->initials() }}
                        </span>
                        <span>{{ auth()->user()->name }}</span>
                        <span class="text-xs text-gray-400">{{ auth()->user()->role->label() }}</span>
                    </button>

                    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 rounded-md border bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <div class="flex flex-1">
                <aside class="w-56 border-r bg-white px-4 py-6 dark:border-gray-700 dark:bg-gray-800">
                    <nav class="flex flex-col gap-1">
                        <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                            Dashboard
                        </a>

                        <a href="{{ route('boards.index') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('boards.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                            Boards
                        </a>

                        <a href="{{ route('ranking') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('ranking') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                            Ranking
                        </a>

                        <a href="{{ route('checkin') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('checkin') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                            Check-in
                        </a>

                        <a href="{{ route('achievements') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('achievements') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                            Achievements
                        </a>

                        <a href="{{ route('titles') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('titles') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                            Titles
                        </a>

                        <a href="{{ route('challenges') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('challenges') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                            Challenges
                        </a>

                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.index') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Admin
                            </a>

                            <a href="{{ route('admin.categories') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.categories') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Categories
                            </a>

                            <a href="{{ route('admin.event-rules') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.event-rules') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Event Rules
                            </a>

                            <a href="{{ route('admin.achievements') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.achievements') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Achievements
                            </a>

                            <a href="{{ route('admin.titles') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.titles') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Titles
                            </a>

                            <a href="{{ route('admin.challenges') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.challenges') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Challenges
                            </a>
                        @endif
                    </nav>
                </aside>

                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
