import { api, ApiError } from './api';
import { stashToasts, toastsOf } from './page-actions';

/**
 * Modal com formulário que envia para a API e, em caso de sucesso, recarrega a página
 * (ou segue para `data.url`, quando a API devolve o endereço do registro criado).
 *
 * O markup define os campos ligados a `form`; quem abre o modal dispara o evento `eventName`
 * com { url, method, form, multipart }.
 */
export function modalForm() {
    return {
        isOpen: false,
        isSaving: false,
        form: {},
        errors: {},
        target: { url: '', method: 'post', multipart: false },

        open({ url, method = 'post', form = {}, multipart = false }) {
            this.target = { url, method, multipart };
            this.form = JSON.parse(JSON.stringify(form));
            this.errors = {};
            this.isOpen = true;
        },

        close() {
            this.isOpen = false;
        },

        async save() {
            this.isSaving = true;
            this.errors = {};

            try {
                const payload = await api[this.target.method](this.target.url, this.body(), { silent: true });

                stashToasts(toastsOf(payload));
                window.location.assign(payload.data?.url ?? window.location.href);
            } catch (error) {
                this.isSaving = false;

                if (!(error instanceof ApiError)) {
                    throw error;
                }

                this.errors = error.errors ?? {};
            }
        },

        /** Arquivos exigem multipart; os demais campos vão como texto (vazio quando ausentes). */
        body() {
            if (!this.target.multipart) {
                return this.form;
            }

            const data = new FormData();

            Object.entries(this.form).forEach(([field, value]) => {
                if (value instanceof File) {
                    data.append(field, value);
                } else if (value !== null && value !== undefined && typeof value !== 'object') {
                    data.append(field, value === true ? '1' : value === false ? '0' : value);
                }
            });

            return data;
        },

        pickFile(event, field) {
            this.form[field] = event.target.files[0] ?? null;
        },

        firstError(field) {
            return this.errors[field]?.[0] ?? null;
        },
    };
}
