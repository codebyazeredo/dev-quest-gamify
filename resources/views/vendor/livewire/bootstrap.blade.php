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
        <nav role="navigation" aria-label="Paginação" class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
            <p class="small text-body-secondary mb-0">
                Mostrando
                <span class="fw-semibold text-body">{{ $paginator->firstItem() }}</span>
                a
                <span class="fw-semibold text-body">{{ $paginator->lastItem() }}</span>
                de
                <span class="fw-semibold text-body">{{ $paginator->total() }}</span>
                resultados
            </p>

            <ul class="pagination pagination-sm mb-0">
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    <button type="button" class="page-link" wire:click="gotoPage(1, '{{ $pageName }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" aria-label="Primeira página" title="Primeira página" @disabled($paginator->onFirstPage())>
                        <i class="bi bi-chevron-double-left"></i>
                    </button>
                </li>

                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    <button type="button" class="page-link" wire:click="previousPage('{{ $pageName }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" aria-label="{{ __('pagination.previous') }}" title="Anterior" @disabled($paginator->onFirstPage())>
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </li>

                <li class="page-item active" aria-current="page">
                    <span class="page-link">Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>
                </li>

                <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                    <button type="button" class="page-link" wire:click="nextPage('{{ $pageName }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" aria-label="{{ __('pagination.next') }}" title="Próximo" @disabled(! $paginator->hasMorePages())>
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </li>

                <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                    <button type="button" class="page-link" wire:click="gotoPage({{ $paginator->lastPage() }}, '{{ $pageName }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" aria-label="Última página" title="Última página" @disabled(! $paginator->hasMorePages())>
                        <i class="bi bi-chevron-double-right"></i>
                    </button>
                </li>
            </ul>
        </nav>
    @endif
</div>
