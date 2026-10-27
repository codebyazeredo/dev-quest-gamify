import { crudTable } from './crud-table';

export function ticketsTable() {
    return crudTable({ endpoint: '/api/tickets', emptyForm: {}, filters: { search: '', status: '' } });
}
