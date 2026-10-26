import { api, ApiError } from './api';

/**
 * Listagem (paginada ou não) com modal de criação/edição, consumindo um recurso REST.
 * O formulário do modal é definido na view e ligado a `form` via x-model.
 * `keyField` identifica a linha na URL de edição/exclusão (padrão: id).
 */
export function crudTable({ endpoint, emptyForm, keyField = 'id', multipart = false, filters = {} }) {
    return {
        rows: [],
        extra: {},
        meta: { current_page: 1, last_page: 1, total: 0 },
        perPage: 10,
        isLoading: false,
        isModalOpen: false,
        editingRow: null,
        form: { ...emptyForm },
        errors: {},
        filters: { ...filters },

        async load(page = this.meta.current_page) {
            this.isLoading = true;

            try {
                const payload = await api.get(endpoint, { ...this.activeFilters(), page, per_page: this.perPage });
                this.rows = payload.data;
                this.meta = payload.meta ?? this.meta;
                this.extra = payload;
            } finally {
                this.isLoading = false;
            }
        },

        /** Filtros vazios não vão na query string. */
        activeFilters() {
            return Object.fromEntries(Object.entries(this.filters).filter(([, value]) => value !== ''));
        },

        applyFilters() {
            return this.load(1);
        },

        changePerPage() {
            return this.load(1);
        },

        openCreate() {
            this.editingRow = null;
            this.form = { ...emptyForm };
            this.errors = {};
            this.isModalOpen = true;
        },

        openEdit(row) {
            this.editingRow = row;
            // Cópia profunda: o formulário não pode alterar arrays da linha (ex.: roles) antes de salvar.
            this.form = JSON.parse(JSON.stringify(row));
            this.errors = {};
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
        },

        async save() {
            this.errors = {};

            const body = multipart ? this.formData() : this.form;

            try {
                if (this.editingRow) {
                    await api.put(`${endpoint}/${this.editingRow[keyField]}`, body);
                } else {
                    await api.post(endpoint, body);
                }
            } catch (error) {
                if (error instanceof ApiError && error.status === 422) {
                    this.errors = error.errors ?? {};
                    return;
                }

                throw error;
            }

            this.closeModal();
            await this.load();
        },

        async remove(row, confirmation) {
            if (!window.confirm(confirmation)) {
                return;
            }

            await api.delete(`${endpoint}/${row[keyField]}`);

            // Ao excluir o último item de uma página, volta para a anterior.
            const page = this.rows.length === 1 ? Math.max(1, this.meta.current_page - 1) : this.meta.current_page;
            await this.load(page);
        },

        /** Campos ausentes viajam vazios; arquivos só são enviados quando escolhidos. */
        formData() {
            const data = new FormData();

            Object.keys(emptyForm).forEach((field) => {
                const value = this.form[field];

                if (value instanceof File) {
                    data.append(field, value);
                } else if (!(value === null && emptyForm[field] === null)) {
                    data.append(field, value ?? '');
                }
            });

            return data;
        },

        /** Ação pontual sobre uma linha (ex.: ativar/desativar, duplicar) via POST em `${endpoint}/{id}/{path}`. */
        async postAction(row, path) {
            await api.post(`${endpoint}/${row[keyField]}/${path}`);
            await this.load();
        },

        firstError(field) {
            return this.errors[field]?.[0] ?? null;
        },
    };
}
