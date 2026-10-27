import Alpine from 'alpinejs';
import { Tooltip } from 'bootstrap';
import './kanban.js';
import './theme.js';
import './sidebar.js';
import './clipboard.js';
import { challengesTable } from './challenges-table.js';
import { clientsTable } from './clients-table.js';
import { crudTable } from './crud-table.js';
import { formatCnpj } from './masks.js';
import { modalForm } from './modal-form.js';
import { notificationBell } from './notification-bell.js';
import { pageActions, showStashedToasts } from './page-actions.js';
import { peopleTable } from './people-table.js';
import { profileModal } from './profile-modal.js';
import { rolesTable } from './roles-table.js';
import { settingsForm } from './settings-form.js';
import { taskModal } from './task-modal.js';
import { ticketPage } from './ticket-page.js';
import { ticketsTable } from './tickets-table.js';
import { taskPage } from './task-page.js';

// Os componentes são registrados em "alpine:init", disparado por Alpine.start() no fim deste arquivo.
document.addEventListener('alpine:init', () => {
    window.Alpine.data('crudTable', crudTable);
    window.Alpine.data('rolesTable', rolesTable);
    window.Alpine.data('peopleTable', peopleTable);
    window.Alpine.data('challengesTable', challengesTable);
    window.Alpine.data('clientsTable', clientsTable);
    window.Alpine.data('ticketsTable', ticketsTable);
    window.Alpine.data('ticketPage', ticketPage);
    window.Alpine.data('cnpjInput', (cnpj) => ({ cnpj, formatCnpj }));
    window.Alpine.data('settingsForm', settingsForm);
    window.Alpine.data('notificationBell', notificationBell);
    window.Alpine.data('pageActions', pageActions);
    window.Alpine.data('modalForm', modalForm);
    window.Alpine.data('taskModal', taskModal);
    window.Alpine.data('taskPage', taskPage);
    window.Alpine.data('profileModal', profileModal);
});

// Avisos de uma ação que recarregou a página só podem aparecer depois que a pilha de toasts existir.
document.addEventListener('alpine:initialized', showStashedToasts);

// Delegado ao body para valer também para elementos recriados dinamicamente.
document.addEventListener('DOMContentLoaded', () => {
    new Tooltip(document.body, { selector: '[data-bs-toggle="tooltip"]', trigger: 'hover', placement: 'bottom' });
});

// Botões são recriados após a ação; sem isso o balão do botão clicado ficaria preso na tela.
document.addEventListener('click', () => {
    document.querySelectorAll('.tooltip').forEach((tooltip) => tooltip.remove());
});

window.Alpine = Alpine;
Alpine.start();
