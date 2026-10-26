import { api } from './api';
import { modalForm } from './modal-form';

/** Modal "Editar meus dados": carrega os dados atuais do usuário antes de abrir. */
export function profileModal() {
    return {
        ...modalForm(),

        genders: [],

        async openProfile() {
            const payload = await api.get('/api/me/profile');

            this.genders = payload.genders;
            this.open({ url: '/api/me/profile', method: 'put', multipart: true, form: payload.data });
        },

        photoPreview() {
            return this.form.foto instanceof File ? URL.createObjectURL(this.form.foto) : this.form.foto_url;
        },
    };
}
