@props(['title', 'subtitle' => null, 'back' => null, 'backLabel' => 'Voltar'])

<div class="mb-5 flex flex-col gap-3 border-b border-line pb-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex min-w-0 items-start gap-3">
        @if ($back)
            <x-back-link :href="$back" :label="$backLabel" class="mt-0.5" />
        @endif

        <div class="min-w-0">
            <h1 class="text-xl font-bold tracking-tight text-ink">{{ $title }}</h1>
            @if ($subtitle)
                <div class="mt-0.5 text-sm text-ink-muted">
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
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
