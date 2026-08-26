<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php $appSettings = \App\Models\AppSetting::current(); @endphp

        <title>{{ $title ?? $appSettings->company_name ?: config('app.name', 'DevQuestGamify') }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="h-screen overflow-hidden bg-gray-100 dark:bg-gray-900">
        <div class="flex h-screen flex-col">
            <nav class="flex items-center justify-between border-b bg-white px-6 py-3 dark:border-gray-700 dark:bg-gray-800">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-lg font-semibold text-gray-800 dark:text-gray-100">
                    @if ($appSettings->logo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($appSettings->logo_path) }}" alt="" class="h-7 w-auto">
                    @endif
                    {{ $appSettings->company_name ?: config('app.name', 'DevQuestGamify') }}
                </a>

                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 rounded-md px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                        @if (auth()->user()->person?->foto_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->person->foto_path) }}" alt="" class="h-8 w-8 rounded-full object-cover">
                        @else
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-xs font-semibold text-white">
                                {{ auth()->user()->initials() }}
                            </span>
                        @endif
                        <span>{{ auth()->user()->name }}</span>
                        @if (auth()->user()->selectedTitle)
                            <span class="flex items-center gap-1 text-xs font-medium text-amber-600 dark:text-amber-400">
                                <x-icon :name="auth()->user()->selectedTitle->icon" class="h-3.5 w-3.5" />
                                {{ auth()->user()->selectedTitle->name }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">{{ auth()->user()->getRoleNames()->map(fn ($role) => \App\Enums\UserRole::labelFor($role))->join(', ') }}</span>
                        @endif
                    </button>

                    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 rounded-md border bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <div class="flex flex-1 min-h-0">
                <aside class="w-56 shrink-0 overflow-y-auto border-r bg-white px-4 py-6 dark:border-gray-700 dark:bg-gray-800">
                    <nav class="flex flex-col gap-1">
                        <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                            Minha conta
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

                        <a href="{{ route('challenges') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('challenges') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                            Desafios
                        </a>

                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.index') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Admin
                            </a>

                            <a href="{{ route('admin.users') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.users') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Usuários
                            </a>

                            <a href="{{ route('admin.people') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.people') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Pessoas
                            </a>

                            <a href="{{ route('admin.categories') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.categories') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Categorias
                            </a>

                            <a href="{{ route('admin.event-rules') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.event-rules') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Regras de XP
                            </a>

                            <a href="{{ route('admin.priority-rules') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.priority-rules') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Gravidade
                            </a>

                            <a href="{{ route('admin.achievements') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.achievements') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Conquistas
                            </a>

                            <a href="{{ route('admin.titles') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.titles') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Títulos
                            </a>

                            <a href="{{ route('admin.challenges') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.challenges') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Desafios
                            </a>

                            <a href="{{ route('admin.settings') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.settings') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Configurações
                            </a>

                            <a href="{{ route('admin.roles') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.roles') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                                Roles
                            </a>
                        @endif
                    </nav>
                </aside>

                <main class="flex min-w-0 min-h-0 flex-1 flex-col overflow-y-auto p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @php
            $pendingToasts = collect(session()->pull('pending_toasts', []))
                ->values()
                ->map(fn ($toast, $index) => [...$toast, 'id' => $index])
                ->all();
        @endphp

        <div
            x-data="{
                toasts: @js($pendingToasts),
                addToast(toast) {
                    const t = { id: Date.now() + Math.random(), ...toast };
                    this.toasts.push(t);
                    this.scheduleRemoval(t);
                },
                scheduleRemoval(t) {
                    setTimeout(() => { this.toasts = this.toasts.filter(x => x.id !== t.id) }, 5000);
                }
            }"
            x-init="toasts.forEach(t => scheduleRemoval(t))"
            x-on:toast.window="addToast($event.detail.toast)"
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
                        'border-rose-300 bg-rose-50 dark:border-rose-700 dark:bg-rose-900/30': t.type === 'error',
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
                    <span x-show="t.type === 'error'" class="text-rose-500">
                        <x-icon name="alert" class="h-5 w-5" />
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
