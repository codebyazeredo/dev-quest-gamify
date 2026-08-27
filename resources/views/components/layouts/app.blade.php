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
    <body class="h-screen overflow-hidden bg-surface text-ink">
        <div x-data="{ sidebarOpen: false }" class="flex h-screen flex-col">
            <nav class="flex items-center justify-between bg-primary px-4 py-3 sm:px-6">
                <div class="flex items-center gap-3">
                    <button type="button" @click="sidebarOpen = true" class="rounded-lg p-1.5 text-white/70 transition-colors hover:bg-white/10 lg:hidden" aria-label="Abrir menu">
                        <x-icon name="menu" class="h-5 w-5" />
                    </button>

                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-lg font-bold tracking-tight text-white">
                        @if ($appSettings->logoUrl())
                            <img src="{{ $appSettings->logoUrl() }}" alt="" class="h-7 w-auto">
                        @endif
                        {{ $appSettings->displayName() }}
                    </a>
                </div>

                <div class="flex items-center gap-1">
                    <a href="{{ route('ranking') }}" title="Ranking" aria-label="Ranking" class="hidden h-9 w-9 items-center justify-center rounded-lg transition-colors sm:flex {{ request()->routeIs('ranking') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="trophy" class="h-[18px] w-[18px]" />
                    </a>

                    <a href="{{ route('challenges') }}" title="Desafios" aria-label="Desafios" class="hidden h-9 w-9 items-center justify-center rounded-lg transition-colors sm:flex {{ request()->routeIs('challenges') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="flag" class="h-[18px] w-[18px]" />
                    </a>

                    <a href="{{ route('checkin') }}" title="Check-in" aria-label="Check-in" class="hidden h-9 w-9 items-center justify-center rounded-lg transition-colors sm:flex {{ request()->routeIs('checkin') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="fire" class="h-[18px] w-[18px]" />
                    </a>

                    <div class="relative ml-1" x-data="{ open: false }">
                        <button type="button" @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 rounded-lg px-2 py-1 text-sm text-white transition-colors hover:bg-white/10">
                            @if (auth()->user()->person?->foto_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->person->foto_path) }}" alt="" class="h-8 w-8 rounded-full object-cover">
                            @else
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-semibold text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            @endif
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                            @if (auth()->user()->selectedTitle)
                                <span class="hidden items-center gap-1 text-xs font-medium text-gold sm:flex">
                                    <x-icon :name="auth()->user()->selectedTitle->icon" class="h-3.5 w-3.5" />
                                    {{ auth()->user()->selectedTitle->name }}
                                </span>
                            @else
                                <span class="hidden text-xs text-white/60 sm:inline">{{ auth()->user()->getRoleNames()->map(fn ($role) => \App\Enums\UserRole::labelFor($role))->join(', ') }}</span>
                            @endif
                        </button>

                        <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-48 rounded-xl border border-line bg-card py-1 shadow-lg">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-line/30' }}">
                                Minha conta
                            </a>

                            <hr class="my-1 border-line">

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-ink hover:bg-line/30">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="flex flex-1 min-h-0">
                <div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-ink/50 lg:hidden"></div>

                <aside
                    x-show="sidebarOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 -translate-x-4"
                    class="fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 flex-col bg-ink px-4 py-6 lg:hidden"
                >
                    <div class="min-h-0 flex-1 overflow-y-auto">
                        <x-sidebar-nav />
                    </div>
                    <x-sidebar-footer />
                </aside>

                <aside class="hidden shrink-0 flex-col bg-ink px-4 py-6 lg:flex lg:w-56">
                    <div class="min-h-0 flex-1 overflow-y-auto">
                        <x-sidebar-nav />
                    </div>
                    <x-sidebar-footer />
                </aside>

                <main class="flex min-w-0 min-h-0 flex-1 flex-col overflow-y-auto p-4 sm:p-6">
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
                    if (t.type === 'checkin') return;
                    setTimeout(() => { this.toasts = this.toasts.filter(x => x.id !== t.id) }, 5000);
                }
            }"
            x-init="toasts.forEach(t => scheduleRemoval(t))"
            x-on:toast.window="addToast($event.detail.toast)"
            class="fixed right-4 top-20 z-[60] flex w-80 flex-col gap-2"
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
                        'border-gold ring-2 ring-gold scale-105': t.type === 'level_up',
                        'border-gold/40': t.type === 'achievement',
                        'border-accent/40': t.type === 'challenge',
                        'border-amber-clay/40': t.type === 'streak',
                        'border-terracotta/40': t.type === 'error',
                        'border-forest/40': t.type === 'checkin' || t.type === 'success',
                    }"
                    class="flex items-start gap-3 rounded-xl border p-3 shadow-lg bg-card"
                >
                    <span x-show="t.type === 'level_up'" class="text-gold">
                        <x-icon name="star" class="h-7 w-7" />
                    </span>
                    <span x-show="t.type === 'achievement'" class="text-gold">
                        <x-icon name="trophy" class="h-5 w-5" />
                    </span>
                    <span x-show="t.type === 'challenge'" class="text-accent">
                        <x-icon name="flag" class="h-5 w-5" />
                    </span>
                    <span x-show="t.type === 'streak'" class="text-amber-clay">
                        <x-icon name="fire" class="h-5 w-5" />
                    </span>
                    <span x-show="t.type === 'error'" class="text-terracotta">
                        <x-icon name="alert" class="h-5 w-5" />
                    </span>
                    <span x-show="t.type === 'checkin' || t.type === 'success'" class="text-forest">
                        <x-icon name="check" class="h-5 w-5" />
                    </span>

                    <div class="min-w-0 flex-1">
                        <p
                            x-text="t.title"
                            :class="t.type === 'level_up' ? 'text-base font-bold text-gold' : 'text-sm font-semibold text-ink'"
                        ></p>
                        <p x-text="t.message" class="text-xs text-ink-muted"></p>
                    </div>

                    <button type="button" @click="toasts = toasts.filter(x => x.id !== t.id)" class="text-ink-muted hover:text-ink">
                        &times;
                    </button>
                </div>
            </template>
        </div>

        @livewireScripts
    </body>
</html>
