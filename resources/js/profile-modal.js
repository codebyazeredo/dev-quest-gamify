import { api } from './api';
import { modalForm } from './modal-form';

export function profileModal() {
    return {
        ...modalForm(),

        genders: [],
        extra: { countries: [] },

        async openProfile() {
            const payload = await api.get('/api/me/profile');

            this.genders = payload.genders;
            this.extra = { countries: payload.countries };
            this.open({ url: '/api/me/profile', method: 'put', multipart: true, form: payload.data });
        },

        photoPreview() {
            return this.form.foto instanceof File ? URL.createObjectURL(this.form.foto) : this.form.foto_url;
        },
    };
}
