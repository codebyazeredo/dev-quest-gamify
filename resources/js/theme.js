const STORAGE_KEY = 'theme';

function storedTheme() {
    try {
        return localStorage.getItem(STORAGE_KEY) === 'dark' ? 'dark' : 'light';
    } catch {
        return 'light';
    }
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
}

document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-theme-toggle]')) {
        return;
    }

    const next = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';

    try {
        localStorage.setItem(STORAGE_KEY, next);
    } catch {
        // Sem localStorage a preferência vale apenas para esta página.
    }

    applyTheme(next);
});

document.addEventListener('DOMContentLoaded', () => applyTheme(storedTheme()));
