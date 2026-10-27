{{-- Aplica o tema salvo antes do CSS carregar, evitando o "flash" claro no modo escuro. --}}
<script>
    (function () {
        var theme = 'light';

        try {
            theme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
        } catch (error) {
            theme = 'light';
        }

        document.documentElement.setAttribute('data-bs-theme', theme);

        try {
            document.documentElement.classList.toggle('sidebar-collapsed', localStorage.getItem('sidebar') === 'collapsed');
        } catch (error) {}
    })();
</script>
