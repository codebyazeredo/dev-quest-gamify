@props(['icon', 'label', 'value', 'color' => 'primary'])

<div class="card h-100">
    <div class="card-body d-flex align-items-center gap-3 p-3">
        <x-icon-chip :icon="$icon" :color="$color" />
        <div class="min-w-0">
            <div class="fs-4 fw-bold lh-1">{{ $value }}</div>
            <div class="small text-body-secondary text-truncate mt-1">{{ $label }}</div>
        </div>
    </div>
</div>
