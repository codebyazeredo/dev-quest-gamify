@props(['title', 'subtitle' => null, 'back' => null, 'backLabel' => 'Voltar'])

@push('page-title')
    <div class="min-w-0 lh-sm">
        <h1 class="fs-5 fw-bold mb-0 text-truncate">{{ $title }}</h1>

        @if ($subtitle)
            <div class="small text-body-secondary text-truncate">
                @if ($subtitle instanceof \Illuminate\View\ComponentSlot)
                    {!! $subtitle !!}
                @else
                    {{ $subtitle }}
                @endif
            </div>
        @endif
    </div>
@endpush

<div class="d-flex flex-column flex-sm-row align-items-sm-end justify-content-between gap-3 mb-3">
    <div class="min-w-0">
        @if ($back)
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ $back }}">{{ $backLabel }}</a></li>
                    <li class="breadcrumb-item active text-truncate" aria-current="page">{{ $title }}</li>
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
