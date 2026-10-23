@props(['title', 'subtitle' => null, 'back' => null, 'backLabel' => 'Voltar'])

<div class="d-flex flex-column flex-sm-row align-items-sm-end justify-content-between gap-3 mb-4">
    <div class="min-w-0">
        @if ($back)
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ $back }}">{{ $backLabel }}</a></li>
                    <li class="breadcrumb-item active text-truncate" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
        @endif

        <h1 class="h3 mb-0 text-truncate">{{ $title }}</h1>

        @if ($subtitle)
            <div class="text-body-secondary mt-1">
                @if ($subtitle instanceof \Illuminate\View\ComponentSlot)
                    {!! $subtitle !!}
                @else
                    {{ $subtitle }}
                @endif
            </div>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="d-flex flex-shrink-0 flex-wrap align-items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
