import { crudTable } from './crud-table';

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
    cep: '',
    logradouro: '',
    numero: '',
    cidade: '',
    estado: '',
};

export function peopleTable() {
    return {
        ...crudTable({ endpoint: '/api/people', emptyForm: EMPTY_PERSON, multipart: true }),

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
