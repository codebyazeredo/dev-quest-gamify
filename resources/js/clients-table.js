import { crudTable } from './crud-table';
import { formatCnpj, onlyDigits } from './masks';

const EMPTY_CLIENT = { razao_social: '', cnpj: '', email: '', is_active: true };

export function clientsTable() {
    const table = crudTable({ endpoint: '/api/clients', emptyForm: EMPTY_CLIENT });

    return {
        ...table,

        formatCnpj,

        /** O CNPJ é guardado só com dígitos; a máscara existe apenas na tela. */
        async save() {
            this.form.cnpj = onlyDigits(this.form.cnpj);

            await table.save.call(this);

            if (this.isModalOpen) {
                this.form.cnpj = formatCnpj(this.form.cnpj);
            }
        },

        openEdit(row) {
            table.openEdit.call(this, row);

            this.form.cnpj = formatCnpj(row.cnpj);
        },
    };
}
