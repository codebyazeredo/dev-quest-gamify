import { api } from './api';
import { crudTable } from './crud-table';

export function rolesTable() {
    return {
        ...crudTable({ endpoint: '/api/roles', emptyForm: { name: '' } }),

        hasPermission(role, permission) {
            return role.permission_ids.includes(permission.id);
        },

        async togglePermission(role, permission) {
            const payload = await api.put(`/api/roles/${role.id}/permissions/${permission.id}`, {}, { silent: true });

            this.rows = this.rows.map((row) => (row.id === role.id ? payload.data : row));
        },
    };
}
