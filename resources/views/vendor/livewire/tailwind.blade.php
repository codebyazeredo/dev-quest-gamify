@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';

$pageName = $paginator->getPageName();
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-ink-muted">
                Mostrando
                <span class="font-medium text-ink">{{ $paginator->firstItem() }}</span>
                a
                <span class="font-medium text-ink">{{ $paginator->lastItem() }}</span>
                de
                <span class="font-medium text-ink">{{ $paginator->total() }}</span>
                resultados
            </p>

            <div class="flex items-center gap-1">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Primeira página" class="flex h-8 w-8 items-center justify-center rounded-lg border border-line text-ink-muted/50">
                        <x-icon name="chevrons-left" class="h-4 w-4" />
                    </span>
                @else
                    <button type="button" wire:click="gotoPage(1, '{{ $pageName }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" aria-label="Primeira página" title="Primeira página" class="flex h-8 w-8 items-center justify-center rounded-lg border border-line text-ink-muted hover:bg-line/20 hover:text-ink">
                        <x-icon name="chevrons-left" class="h-4 w-4" />
                    </button>
                @endif

                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="flex h-8 w-8 items-center justify-center rounded-lg border border-line text-ink-muted/50">
                        <x-icon name="chevron-down" class="h-4 w-4 rotate-90" />
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $pageName }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" aria-label="{{ __('pagination.previous') }}" title="Anterior" class="flex h-8 w-8 items-center justify-center rounded-lg border border-line text-ink-muted hover:bg-line/20 hover:text-ink">
                        <x-icon name="chevron-down" class="h-4 w-4 rotate-90" />
                    </button>
                @endif

                <span class="flex h-8 min-w-[5.5rem] items-center justify-center rounded-lg bg-primary px-2 text-sm font-semibold text-white">
                    Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}
                </span>

                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $pageName }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" aria-label="{{ __('pagination.next') }}" title="Próximo" class="flex h-8 w-8 items-center justify-center rounded-lg border border-line text-ink-muted hover:bg-line/20 hover:text-ink">
                        <x-icon name="chevron-down" class="h-4 w-4 -rotate-90" />
                    </button>
                @else
                    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="flex h-8 w-8 items-center justify-center rounded-lg border border-line text-ink-muted/50">
                        <x-icon name="chevron-down" class="h-4 w-4 -rotate-90" />
                    </span>
                @endif

                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="gotoPage({{ $paginator->lastPage() }}, '{{ $pageName }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" aria-label="Última página" title="Última página" class="flex h-8 w-8 items-center justify-center rounded-lg border border-line text-ink-muted hover:bg-line/20 hover:text-ink">
                        <x-icon name="chevrons-right" class="h-4 w-4" />
                    </button>
                @else
                    <span aria-disabled="true" aria-label="Última página" class="flex h-8 w-8 items-center justify-center rounded-lg border border-line text-ink-muted/50">
                        <x-icon name="chevrons-right" class="h-4 w-4" />
                    </span>
                @endif
            </div>
        </nav>
    @endif
</div>
