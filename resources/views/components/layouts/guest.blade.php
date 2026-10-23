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
                <div class="col-lg-5 col-xl-4 bg-body d-flex flex-column justify-content-center px-4 px-sm-5 py-5 order-2 order-lg-1">
                    <div class="w-100 mx-auto" style="max-width: 24rem;">
                        @if (session('status'))
                            <div class="alert alert-success d-flex align-items-start gap-2" role="alert">
                                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                                <div>{{ session('status') }}</div>
                            </div>
                        @endif

                        {{ $slot }}
                    </div>

                    <x-footer class="w-100 mx-auto mt-5" style="max-width: 24rem;" />
                </div>

                <div class="col-lg-7 col-xl-8 bg-primary bg-gradient text-bg-primary d-flex flex-column align-items-center justify-content-center gap-4 px-4 py-5 text-center order-1 order-lg-2">
                    @if ($appSettings->logoUrl())
                        @php
                            $logoMaxWidth = match ($appSettings->logo_size) {
                                \App\Enums\LogoSize::SMALL => '14rem',
                                \App\Enums\LogoSize::MEDIUM => '20rem',
                                \App\Enums\LogoSize::LARGE => '28rem',
                            };
                        @endphp
                        <img src="{{ $appSettings->logoUrl() }}" alt="{{ $appSettings->displayName() }}" class="img-fluid" style="max-width: min(100%, {{ $logoMaxWidth }}); max-height: 40vh;">
                    @else
                        <h2 class="display-5 fw-bold mb-0">{{ $appSettings->displayName() }}</h2>
                    @endif

                    <p class="d-none d-lg-block fs-4 fw-medium mb-0 opacity-90" style="max-width: 32rem;">
                        Transforme tarefas em conquistas: gamificação de verdade para o seu time de desenvolvimento.
                    </p>

                    <ul class="list-unstyled d-none d-lg-flex gap-4 mb-0 small fw-medium">
                        <li class="d-flex align-items-center gap-2"><i class="bi bi-kanban fs-5" aria-hidden="true"></i> Kanban</li>
                        <li class="d-flex align-items-center gap-2"><i class="bi bi-lightning-charge-fill fs-5" aria-hidden="true"></i> XP e níveis</li>
                        <li class="d-flex align-items-center gap-2"><i class="bi bi-trophy-fill fs-5" aria-hidden="true"></i> Ranking</li>
                    </ul>
                </div>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
