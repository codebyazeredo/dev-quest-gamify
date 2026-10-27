import { showToast } from './api';

async function copyText(text) {
    try {
        await navigator.clipboard.writeText(text);
    } catch {
        const field = document.createElement('textarea');
        field.value = text;
        field.style.position = 'fixed';
        field.style.opacity = '0';
        document.body.appendChild(field);
        field.select();
        document.execCommand('copy');
        field.remove();
    }
}

document.addEventListener('click', async (event) => {
    const trigger = event.target.closest('[data-copy]');

    if (!trigger || !trigger.dataset.copy) {
        return;
    }

    event.preventDefault();
    await copyText(trigger.dataset.copy);

    showToast({ type: 'success', title: trigger.dataset.copyLabel ?? 'Copiado', message: trigger.dataset.copy });
});
