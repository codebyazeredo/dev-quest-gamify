@props(['paginator' => null])

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="app-thead">
                <tr class="small text-body-secondary text-uppercase">
                    {{ $head }}
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if ($paginator)
        <div class="card-footer bg-body-tertiary d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <x-per-page-selector />
            {{ $paginator->links() }}
        </div>
    @endif
</div>
