import { api, ApiError } from './api';

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

            const page = this.rows.length === 1 ? Math.max(1, this.meta.current_page - 1) : this.meta.current_page;
            await this.load(page);
        },

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

        async postAction(row, path) {
            await api.post(`${endpoint}/${row[keyField]}/${path}`);
            await this.load();
        },

        firstError(field) {
            return this.errors[field]?.[0] ?? null;
        },
    };
}
