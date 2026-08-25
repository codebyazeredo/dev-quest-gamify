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

        <div
            x-data="{ toasts: [] }"
            x-on:toast.window="
                const t = { id: Date.now() + Math.random(), ...$event.detail.toast };
                toasts.push(t);
                setTimeout(() => { toasts = toasts.filter(x => x.id !== t.id) }, 5000);
            "
            class="fixed right-4 top-4 z-50 flex w-80 flex-col gap-2"
        >
            <template x-for="t in toasts" :key="t.id">
                <div
                    x-show="true"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-4"
                    :class="{
                        'border-amber-400 bg-gradient-to-br from-amber-50 to-yellow-100 ring-2 ring-amber-400 scale-105 dark:from-amber-900/40 dark:to-yellow-900/30 dark:border-amber-600': t.type === 'level_up',
                        'border-amber-300 bg-amber-50 dark:border-amber-700 dark:bg-amber-900/30': t.type === 'achievement',
                        'border-indigo-300 bg-indigo-50 dark:border-indigo-700 dark:bg-indigo-900/30': t.type === 'challenge',
                        'border-orange-300 bg-orange-50 dark:border-orange-700 dark:bg-orange-900/30': t.type === 'streak',
                    }"
                    class="flex items-start gap-3 rounded-lg border p-3 shadow-lg"
                >
                    <span x-show="t.type === 'level_up'" class="text-amber-500">
                        <x-icon name="star" class="h-7 w-7" />
                    </span>
                    <span x-show="t.type === 'achievement'" class="text-amber-500">
                        <x-icon name="trophy" class="h-5 w-5" />
                    </span>
                    <span x-show="t.type === 'challenge'" class="text-indigo-500">
                        <x-icon name="flag" class="h-5 w-5" />
                    </span>
                    <span x-show="t.type === 'streak'" class="text-orange-500">
                        <x-icon name="fire" class="h-5 w-5" />
                    </span>

                    <div class="min-w-0 flex-1">
                        <p
                            x-text="t.title"
                            :class="t.type === 'level_up' ? 'text-base font-bold text-amber-700 dark:text-amber-300' : 'text-sm font-semibold text-gray-800 dark:text-gray-100'"
                        ></p>
                        <p x-text="t.message" class="text-xs text-gray-600 dark:text-gray-300"></p>
                    </div>

                    <button type="button" @click="toasts = toasts.filter(x => x.id !== t.id)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        &times;
                    </button>
                </div>
            </template>
        </div>

        @livewireScripts
    </body>
</html>
