const STORAGE_KEY = 'theme';
const systemDarkQuery = window.matchMedia('(prefers-color-scheme: dark)');

function storedPreference() {
    try {
        return localStorage.getItem(STORAGE_KEY) || 'auto';
    } catch {
        return 'auto';
    }
}

function resolveTheme(preference) {
    if (preference === 'auto') {
        return systemDarkQuery.matches ? 'dark' : 'light';
    }

    return preference;
}

function applyPreference(preference) {
    document.documentElement.setAttribute('data-bs-theme', resolveTheme(preference));

    document.querySelectorAll('[data-theme-choice]').forEach((button) => {
        const active = button.dataset.themeChoice === preference;

        button.classList.toggle('active', active);
        button.setAttribute('aria-pressed', String(active));
    });
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-choice]');

    if (!button) {
        return;
    }

    try {
        localStorage.setItem(STORAGE_KEY, button.dataset.themeChoice);
    } catch {
    }

    applyPreference(button.dataset.themeChoice);
});

systemDarkQuery.addEventListener('change', () => {
    if (storedPreference() === 'auto') {
        applyPreference('auto');
    }
});

document.addEventListener('DOMContentLoaded', () => applyPreference(storedPreference()));
