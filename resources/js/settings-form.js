import { api, ApiError } from './api';

const DARK_TEXT = '#212529';
const LIGHT_TEXT = '#ffffff';
const LIGHT_BACKGROUND_THRESHOLD = 165;

function hexToRgb(hex) {
    return [1, 3, 5].map((start) => parseInt(hex.slice(start, start + 2), 16));
}

/** Mesmas variáveis CSS que o componente x-theme-style gera no servidor, aplicadas só à pré-visualização. */
function previewStyle(hex) {
    const [red, green, blue] = hexToRgb(hex);
    const isLight = (red * 299 + green * 587 + blue * 114) / 1000 > LIGHT_BACKGROUND_THRESHOLD;

    return [
        `--bs-primary: ${hex}`,
        `--bs-primary-rgb: ${red}, ${green}, ${blue}`,
        `--app-primary-contrast: ${isLight ? DARK_TEXT : LIGHT_TEXT}`,
        `--bs-primary-text-emphasis: color-mix(in srgb, ${hex} 55%, black)`,
        `--bs-primary-bg-subtle: color-mix(in srgb, ${hex} 12%, white)`,
        `--bs-primary-border-subtle: color-mix(in srgb, ${hex} 35%, white)`,
        `--bs-link-color: ${hex}`,
        `--bs-link-color-rgb: ${red}, ${green}, ${blue}`,
    ].join('; ');
}

export function settingsForm(initial) {
    return {
        form: {
            company_name: initial.company_name ?? '',
            logo: null,
            logo_size: initial.logo_size,
            primary_color: initial.primary_color,
        },
        savedLogoUrl: initial.logo_url,
        errors: {},
        isSaving: false,

        logoPreview() {
            return this.form.logo instanceof File ? URL.createObjectURL(this.form.logo) : this.savedLogoUrl;
        },

        pickLogo(event) {
            this.form.logo = event.target.files[0] ?? null;
        },

        themePreviewStyle() {
            return previewStyle(this.form.primary_color);
        },

        async save() {
            this.errors = {};
            this.isSaving = true;

            const body = new FormData();
            body.append('company_name', this.form.company_name);
            body.append('logo_size', this.form.logo_size);
            body.append('primary_color', this.form.primary_color);

            if (this.form.logo instanceof File) {
                body.append('logo', this.form.logo);
            }

            try {
                const payload = await api.put('/api/settings', body);
                this.savedLogoUrl = payload.data.logo_url;
                this.form.logo = null;
            } catch (error) {
                if (!(error instanceof ApiError && error.status === 422)) {
                    throw error;
                }

                this.errors = error.errors ?? {};
            } finally {
                this.isSaving = false;
            }
        },

        firstError(field) {
            return this.errors[field]?.[0] ?? null;
        },
    };
}
