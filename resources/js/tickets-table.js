import { api } from './api';
import { crudTable } from './crud-table';

const DEFAULT_VIEW = 'open';
const MINUTES_PER_HOUR = 60;

export function ticketsTable() {
    return {
        ...crudTable({
            endpoint: '/api/tickets',
            emptyForm: {},
            filters: { search: '', view: DEFAULT_VIEW, status: '', priority: '', assignee: '', sort: 'due' },
        }),

        setView(view) {
            this.filters.view = view;

            return this.load(1);
        },

        async claim(row) {
            await api.post(`/api/tickets/${row.id}/claim`);

            return this.load();
        },

        exportUrl(baseUrl) {
            return `${baseUrl}?${new URLSearchParams(this.activeFilters())}`;
        },

        formatMinutes(minutes) {
            if (minutes === null || minutes === undefined) {
                return '—';
            }

            return minutes < MINUTES_PER_HOUR
                ? `${minutes} min`
                : `${Math.floor(minutes / MINUTES_PER_HOUR)} h ${minutes % MINUTES_PER_HOUR} min`;
        },
    };
}
