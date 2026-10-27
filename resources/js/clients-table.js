import { crudTable } from './crud-table';
import { formatCnpj, onlyDigits } from './masks';

const EMPTY_CLIENT = {
    razao_social: '',
    cnpj: '',
    email: '',
    business_sector_id: '',
    is_active: true,
    country_id: '',
    cep: '',
    logradouro: '',
    numero: '',
    complemento: '',
    bairro: '',
    cidade: '',
    estado: '',
    referencia: '',
};

export function clientsTable() {
    const table = crudTable({ endpoint: '/api/clients', emptyForm: EMPTY_CLIENT });

    return {
        ...table,

        formatCnpj,

        async save() {
            this.form.cnpj = onlyDigits(this.form.cnpj);

            await table.save.call(this);

            if (this.isModalOpen) {
                this.form.cnpj = formatCnpj(this.form.cnpj);
            }
        },

        openCreate() {
            table.openCreate.call(this);

            this.form.country_id = this.extra.default_country_id ?? '';
        },

        openEdit(row) {
            table.openEdit.call(this, row);

            this.form.cnpj = formatCnpj(row.cnpj);
            this.form.country_id = row.country_id ?? this.extra.default_country_id ?? '';
        },
    };
}
