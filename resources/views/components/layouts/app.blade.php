<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $appSettings = \App\Models\AppSetting::current();
            $currentUser = auth()->user();
            $navigation = [
                'Geral' => [
                    ['route' => 'boards.index', 'active' => 'boards.*', 'label' => 'Quadros', 'icon' => 'bi-kanban'],
                    ['route' => 'dashboard', 'active' => 'dashboard', 'label' => 'Minha conta', 'icon' => 'bi-person-circle'],
                ],
                'Gamificação' => [
                    ['route' => 'ranking', 'active' => 'ranking', 'label' => 'Ranking', 'icon' => 'bi-trophy'],
                    ['route' => 'challenges', 'active' => 'challenges', 'label' => 'Desafios', 'icon' => 'bi-flag'],
                    ['route' => 'checkin', 'active' => 'checkin', 'label' => 'Check-in', 'icon' => 'bi-fire'],
                    ['route' => 'achievements', 'active' => 'achievements', 'label' => 'Conquistas', 'icon' => 'bi-award'],
                    ['route' => 'titles', 'active' => 'titles', 'label' => 'Títulos', 'icon' => 'bi-patch-check'],
                ],
            ];

            if ($currentUser->isAdmin()) {
                $navigation['Geral'][] = ['route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'label' => 'Painel administrativo', 'icon' => 'bi-speedometer2'];
            }
        @endphp

        <title>{{ $title ?? $appSettings->displayName() }}</title>

        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/x-icon" href="/favicon.ico" sizes="any">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <x-theme-init />

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <x-theme-style :color="$appSettings->primaryColor()" />

        @livewireStyles
    </head>
    <body>
        <div class="d-flex vh-100 overflow-hidden">
            <nav class="app-rail d-none d-lg-flex flex-column align-items-center flex-shrink-0 py-3 gap-2" aria-label="Atalhos">
                <a href="{{ route('boards.index') }}" class="brand-tile text-decoration-none mb-2" title="{{ $appSettings->displayName() }}" aria-label="Início">
                    @if ($appSettings->logoUrl())
                        <img src="{{ $appSettings->logoUrl() }}" alt="">
                    @else
                        <i class="bi bi-controller fs-4 text-primary" aria-hidden="true"></i>
                    @endif
                </a>

                <div class="mt-auto">
                    <div class="dropend">
                        <button type="button" class="btn p-0 border-0 rounded-circle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu do usuário">
                            <x-avatar :user="$currentUser" class="border border-2 border-white border-opacity-50" />
                        </button>

                        <ul class="dropdown-menu shadow">
                            <li class="px-3 py-2">
                                <div class="fw-semibold">{{ $currentUser->name }}</div>
                                <div class="small text-body-secondary">{{ $currentUser->email }}</div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Sair</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <aside class="app-sidebar flex-shrink-0">
              <div class="offcanvas-lg offcanvas-start h-100 d-flex flex-column" id="app-sidebar" tabindex="-1" aria-label="Menu principal">
                <div class="offcanvas-header d-lg-none border-bottom">
                    <span class="fw-semibold">Menu</span>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#app-sidebar" aria-label="Fechar"></button>
                </div>

                <div class="offcanvas-body d-flex flex-column p-3 overflow-y-auto h-100">
                    <a href="{{ route('boards.index') }}" class="d-block text-body text-decoration-none px-2 mb-2">
                        <span class="d-block fw-semibold text-truncate">{{ $appSettings->displayName() }}</span>
                        <span class="d-block small text-body-secondary text-truncate">Gestão gamificada</span>
                    </a>

                    <nav class="flex-grow-1">
                        @foreach ($navigation as $heading => $items)
                            <div class="sidebar-heading">{{ $heading }}</div>
                            <ul class="nav flex-column gap-1">
                                @foreach ($items as $item)
                                    <li>
                                        <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['active']) ? 'active' : '' }}" @if (request()->routeIs($item['active'])) aria-current="page" @endif>
                                            <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
                                            {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach

                    </nav>

                    <div class="border-top pt-3 mt-3">
                        @can('accessAdminPanel', \App\Models\User::class)
                            <a href="{{ route('admin.index') }}" class="nav-link {{ request()->routeIs('admin.*') && ! request()->routeIs('admin.dashboard') ? 'active' : '' }}" @if (request()->routeIs('admin.*') && ! request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                                <i class="bi bi-gear" aria-hidden="true"></i>
                                Configurações
                            </a>
                        @endcan

                        <form method="POST" action="{{ route('logout') }}" class="d-lg-none">
                            @csrf
                            <button type="submit" class="nav-link w-100 border-0 bg-transparent"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Sair</button>
                        </form>

                        <x-theme-switch class="mt-3" />

                        <x-footer class="text-center mt-3" />
                    </div>
                </div>
              </div>
            </aside>

            <div class="d-flex flex-column flex-grow-1 min-w-0">
                <header class="navbar bg-primary d-lg-none px-3" style="color: var(--app-primary-contrast);">
                    <button type="button" class="rail-item border-0 bg-transparent text-reset" data-bs-toggle="offcanvas" data-bs-target="#app-sidebar" aria-controls="app-sidebar" aria-label="Abrir menu">
                        <i class="bi bi-list fs-3" aria-hidden="true"></i>
                    </button>
                    <span class="fw-semibold text-truncate">{{ $appSettings->displayName() }}</span>
                    <x-avatar :user="$currentUser" />
                </header>

                <main class="flex-grow-1 min-h-0 overflow-auto d-flex flex-column px-3 px-lg-4 py-3 py-lg-4">
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
                styles: {
                    level_up: { icon: 'bi-stars', variant: 'warning' },
                    achievement: { icon: 'bi-trophy-fill', variant: 'warning' },
                    challenge: { icon: 'bi-flag-fill', variant: 'info' },
                    streak: { icon: 'bi-fire', variant: 'danger' },
                    error: { icon: 'bi-exclamation-triangle-fill', variant: 'danger' },
                    checkin: { icon: 'bi-check-circle-fill', variant: 'success' },
                    success: { icon: 'bi-check-circle-fill', variant: 'success' },
                },
                styleOf(toast) { return this.styles[toast.type] ?? { icon: 'bi-info-circle-fill', variant: 'primary' } },
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
            class="toast-stack toast-container position-fixed top-0 end-0 p-3 d-flex flex-column gap-2"
            aria-live="polite"
        >
            <template x-for="t in toasts" :key="t.id">
                <div
                    x-show="true"
                    x-transition.opacity.duration.250ms
                    class="toast show w-100 border-0 shadow"
                    :class="t.type === 'level_up' ? 'border border-2 border-warning' : ''"
                    role="status"
                >
                    <div class="toast-body d-flex align-items-start gap-3">
                        <span class="icon-chip icon-chip-sm" :class="`bg-${styleOf(t).variant}-subtle text-${styleOf(t).variant}-emphasis`">
                            <i class="bi" :class="styleOf(t).icon" aria-hidden="true"></i>
                        </span>

                        <div class="min-w-0 flex-grow-1">
                            <p x-text="t.title" class="mb-0 fw-semibold" :class="t.type === 'level_up' ? 'fs-6 text-warning-emphasis' : 'small'"></p>
                            <p x-text="t.message" class="mb-0 small text-body-secondary"></p>
                        </div>

                        <button type="button" class="btn-close small" @click="toasts = toasts.filter(x => x.id !== t.id)" aria-label="Fechar"></button>
                    </div>
                </div>
            </template>
        </div>

        @livewireScripts
    </body>
</html>
