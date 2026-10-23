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
    <body>
        <div class="container-fluid">
            <div class="row min-vh-100">
                <div class="col-lg-5 col-xl-4 bg-body d-flex flex-column justify-content-between px-4 px-sm-5 py-4 order-2 order-lg-1">
                    <div class="d-flex align-items-center gap-2 fw-semibold">
                        @if ($appSettings->logoUrl())
                            <img src="{{ $appSettings->logoUrl() }}" alt="" style="height: 1.75rem; width: auto;">
                        @endif
                        <span class="text-truncate">{{ $appSettings->displayName() }}</span>
                    </div>

                    <div class="w-100 mx-auto py-5" style="max-width: 22rem;">
                        @if (session('status'))
                            <div class="alert alert-success d-flex align-items-start gap-2" role="alert">
                                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                                <div>{{ session('status') }}</div>
                            </div>
                        @endif

                        {{ $slot }}
                    </div>

                    <x-footer class="w-100" />
                </div>

                <div class="auth-showcase col-lg-7 col-xl-8 d-none d-lg-flex flex-column align-items-center justify-content-center gap-4 p-5 text-center text-white order-1 order-lg-2" data-bs-theme="dark">
                    @if ($appSettings->logoUrl())
                        @php
                            $logoMaxWidth = match ($appSettings->logo_size) {
                                \App\Enums\LogoSize::SMALL => '14rem',
                                \App\Enums\LogoSize::MEDIUM => '20rem',
                                \App\Enums\LogoSize::LARGE => '28rem',
                            };
                        @endphp
                        <img src="{{ $appSettings->logoUrl() }}" alt="{{ $appSettings->displayName() }}" class="img-fluid position-relative" style="max-width: min(100%, {{ $logoMaxWidth }}); max-height: 38vh;">
                    @else
                        <h2 class="display-5 fw-bold mb-0 position-relative">{{ $appSettings->displayName() }}</h2>
                    @endif

                    <p class="fs-4 fw-medium mb-0 position-relative" style="max-width: 30rem;">
                        Transforme tarefas em conquistas: gamificação de verdade para o seu time de desenvolvimento.
                    </p>

                    <ul class="list-unstyled d-flex gap-3 mb-0 small position-relative">
                        <li class="d-flex align-items-center gap-2 border border-light-subtle border-opacity-25 rounded-pill px-3 py-2"><i class="bi bi-kanban" aria-hidden="true"></i> Kanban</li>
                        <li class="d-flex align-items-center gap-2 border border-light-subtle border-opacity-25 rounded-pill px-3 py-2"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i> XP e níveis</li>
                        <li class="d-flex align-items-center gap-2 border border-light-subtle border-opacity-25 rounded-pill px-3 py-2"><i class="bi bi-trophy-fill" aria-hidden="true"></i> Ranking</li>
                    </ul>
                </div>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
