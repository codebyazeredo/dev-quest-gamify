const STORAGE_KEY = 'sidebar';

document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-sidebar-toggle]')) {
        return;
    }

    const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');

    try {
        localStorage.setItem(STORAGE_KEY, isCollapsed ? 'collapsed' : 'expanded');
    } catch {
        // Sem localStorage a preferência vale apenas para esta página.
    }
});
