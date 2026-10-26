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
                <a class="page-link" href="{{ $paginator->url(1) }}" aria-label="Primeira página" title="Primeira página" @if ($paginator->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                    <i class="bi bi-chevron-double-left"></i>
                </a>
            </li>

            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" title="Anterior" @if ($paginator->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <li class="page-item active" aria-current="page">
                <span class="page-link">Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>
            </li>

            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" title="Próximo" @unless ($paginator->hasMorePages()) tabindex="-1" aria-disabled="true" @endunless>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>

            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" aria-label="Última página" title="Última página" @unless ($paginator->hasMorePages()) tabindex="-1" aria-disabled="true" @endunless>
                    <i class="bi bi-chevron-double-right"></i>
                </a>
            </li>
        </ul>
    </nav>
@endif
