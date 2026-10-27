<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php $appSettings = \App\Models\AppSetting::current(); @endphp

        <title>{{ $title ?? 'Portal do cliente' }} · {{ $appSettings->displayName() }}</title>

        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/x-icon" href="/favicon.ico" sizes="any">

        <x-theme-init />

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <x-theme-style :color="$appSettings->primaryColor()" />
    </head>
    <body>
        <header class="app-topbar d-flex align-items-center gap-3 px-3 px-lg-4 border-bottom bg-body">
            <a href="{{ route('portal.tickets.index') }}" class="d-flex align-items-center gap-2 text-body text-decoration-none fw-semibold">
                @if ($appSettings->logoUrl())
                    <img src="{{ $appSettings->logoUrl() }}" alt="" style="height: 1.75rem; width: auto;">
                @else
                    <i class="bi bi-controller text-primary" aria-hidden="true"></i>
                @endif
                {{ $appSettings->displayName() }} <span class="text-body-secondary fw-normal d-none d-sm-inline">· Portal do cliente</span>
            </a>

            @isset($client)
                <div class="ms-auto d-flex align-items-center gap-2">
                    <span class="small text-body-secondary d-none d-sm-inline">{{ $client->razao_social }}</span>
                    <form method="POST" action="{{ route('portal.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-icon" title="Sair" aria-label="Sair"><i class="bi bi-box-arrow-right" aria-hidden="true"></i></button>
                    </form>
                </div>
            @endisset
        </header>

        <main class="container py-4" style="max-width: 56rem;">
            @if (session('status'))
                <div class="alert alert-success d-flex align-items-start gap-2" role="alert">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            {{ $slot }}
        </main>

        <x-footer class="justify-content-center pb-4" />
    </body>
</html>
