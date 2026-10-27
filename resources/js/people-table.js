import { crudTable } from './crud-table';
import { formatCpf, formatPhone, onlyDigits } from './masks';

const EMPTY_PERSON = {
    nome: '',
    cpf: '',
    rg: '',
    nascimento: '',
    sexo: '',
    email: '',
    telefone1: '',
    telefone2: '',
    foto: null,
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

const MASKED_FIELDS = ['cpf', 'telefone1', 'telefone2'];

export function peopleTable() {
    const table = crudTable({ endpoint: '/api/people', emptyForm: EMPTY_PERSON, multipart: true });

    return {
        ...table,

        formatCpf,
        formatPhone,

        /** CPF e telefones são guardados só com dígitos; a máscara existe apenas na tela. */
        formData() {
            const data = table.formData.call(this);

            MASKED_FIELDS.forEach((field) => data.set(field, onlyDigits(data.get(field))));

            return data;
        },

        openCreate() {
            table.openCreate.call(this);

            this.form.country_id = this.extra.default_country_id ?? '';
        },

        openEdit(row) {
            table.openEdit.call(this, row);

            this.form.cpf = formatCpf(row.cpf);
            this.form.telefone1 = formatPhone(row.telefone1);
            this.form.telefone2 = formatPhone(row.telefone2);
        },

        /** Foto escolhida no formulário ou, na edição, a já cadastrada. */
        photoPreview() {
            if (this.form.foto instanceof File) {
                return URL.createObjectURL(this.form.foto);
            }

            return this.editingRow?.foto_url ?? null;
        },

        pickPhoto(event) {
            this.form.foto = event.target.files[0] ?? null;
        },
    };
}
