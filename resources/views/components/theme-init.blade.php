<script>
    (function () {
        try {
            var preference = localStorage.getItem('theme') || 'auto';
            var dark = preference === 'dark' || (preference === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
        } catch (error) {
            document.documentElement.setAttribute('data-bs-theme', 'light');
        }
    })();
</script>
