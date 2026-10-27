<footer {{ $attributes->merge(['class' => 'app-footer']) }}>
    <i class="bi bi-code-slash app-footer-icon" aria-hidden="true"></i>
    <span class="app-footer-text sidebar-label">
        <span class="app-footer-brand">Code<strong>by</strong>azeredo<sup>&reg;</sup></span>
        <span class="app-footer-year">&copy; {{ now()->year }}</span>
    </span>
</footer>
