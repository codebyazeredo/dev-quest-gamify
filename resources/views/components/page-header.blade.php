@props(['title', 'subtitle' => null, 'back' => null, 'backLabel' => 'Voltar'])

<div class="d-flex flex-column flex-sm-row align-items-sm-end justify-content-between gap-3 mb-3">
    <div class="min-w-0">
        <h1 class="page-title fs-4 fw-bold mb-0">{{ $title }}</h1>

        @if ($subtitle)
            <div class="page-subtitle small text-body-secondary">
                @if ($subtitle instanceof \Illuminate\View\ComponentSlot)
                    {!! $subtitle !!}
                @else
                    {{ $subtitle }}
                @endif
            </div>
        @endif

        @if ($back)
            <nav aria-label="breadcrumb" class="page-header-crumb mt-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ $back }}">{{ $backLabel }}</a></li>
                    <li class="breadcrumb-item active text-truncate d-none d-md-block" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="d-flex flex-shrink-0 flex-wrap align-items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
