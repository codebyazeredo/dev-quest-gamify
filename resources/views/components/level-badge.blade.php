@props(['level'])

<span class="badge rounded-pill level-badge level-tier-{{ \App\Models\Level::tierOf($level) }} d-inline-flex align-items-center gap-1 px-3 py-2">
    <i class="bi bi-lightning-charge-fill" aria-hidden="true"></i>
    NÍVEL {{ $level }}
</span>
