import { Tooltip } from 'bootstrap';
import './kanban.js';
import './theme.js';

// Delegado ao body para valer também para elementos recriados pelo Livewire.
document.addEventListener('DOMContentLoaded', () => {
    new Tooltip(document.body, { selector: '[data-bs-toggle="tooltip"]', trigger: 'hover', placement: 'bottom' });
});

// O Livewire recria botões após a ação; sem isso o balão do botão clicado ficaria preso na tela.
document.addEventListener('click', () => {
    document.querySelectorAll('.tooltip').forEach((tooltip) => tooltip.remove());
});
