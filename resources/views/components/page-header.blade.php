@props(['title', 'subtitle' => null, 'back' => null, 'backLabel' => 'Voltar'])

<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3 min-w-0">
        @if ($back)
            <x-back-link :href="$back" :label="$backLabel" />
        @endif

        <div class="min-w-0">
            <h1 class="h3 mb-0 text-truncate">{{ $title }}</h1>
            @if ($subtitle)
                <div class="text-body-secondary small mt-1">
                    @if ($subtitle instanceof \Illuminate\View\ComponentSlot)
                        {!! $subtitle !!}
                    @else
                        {{ $subtitle }}
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if ($slot->isNotEmpty())
        <div class="d-flex flex-shrink-0 flex-wrap align-items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
