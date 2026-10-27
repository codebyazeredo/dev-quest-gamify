import { showToast } from './api';

async function copyText(text) {
    try {
        await navigator.clipboard.writeText(text);
    } catch {
        // Sem permissão de área de transferência (ex.: página sem HTTPS), usa o método antigo.
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

// Qualquer elemento com data-copy copia o valor ao ser clicado; data-copy-label personaliza o aviso.
document.addEventListener('click', async (event) => {
    const trigger = event.target.closest('[data-copy]');

    if (!trigger || !trigger.dataset.copy) {
        return;
    }

    event.preventDefault();
    await copyText(trigger.dataset.copy);

    showToast({ type: 'success', title: trigger.dataset.copyLabel ?? 'Copiado', message: trigger.dataset.copy });
});
