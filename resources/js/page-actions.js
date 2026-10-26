import { api, ApiError, showToast } from './api';

const PENDING_TOASTS_KEY = 'pendingToasts';

/** Guarda os toasts para exibi-los depois que a página for recarregada. */
export function stashToasts(toasts) {
    try {
        sessionStorage.setItem(PENDING_TOASTS_KEY, JSON.stringify(toasts));
    } catch {
        // Sem sessionStorage o aviso simplesmente não aparece após o recarregamento.
    }
}

export function showStashedToasts() {
    try {
        const stashed = JSON.parse(sessionStorage.getItem(PENDING_TOASTS_KEY) ?? '[]');
        sessionStorage.removeItem(PENDING_TOASTS_KEY);
        stashed.forEach(showToast);
    } catch {
        // Conteúdo inválido no storage é descartado.
    }
}

/** Toasts devolvidos pela API: a mensagem de sucesso mais os de gamificação (level up, conquistas etc.). */
export function toastsOf(payload) {
    const successToast = payload.message ? [{ type: 'success', title: payload.message, message: '' }] : [];

    return [...successToast, ...(payload.toasts ?? [])];
}

function request(method, url, body) {
    return method === 'delete'
        ? api.delete(url, { silent: true })
        : api[method](url, body, { silent: true });
}

/**
 * Ações de uma linha/botão que chamam a API e recarregam a página para refletir o novo estado.
 * Os erros já são exibidos pelo cliente da API; aqui só interrompemos o fluxo.
 */
export function pageActions() {
    return {
        isBusy: false,
        errors: {},

        async act(method, url, { body = {}, confirmation = null } = {}) {
            if (confirmation && !window.confirm(confirmation)) {
                return null;
            }

            this.isBusy = true;
            this.errors = {};

            try {
                const payload = await request(method, url, body);
                stashToasts(toastsOf(payload));
                window.location.reload();

                return payload;
            } catch (error) {
                this.isBusy = false;

                if (!(error instanceof ApiError)) {
                    throw error;
                }

                this.errors = error.errors ?? {};

                return null;
            }
        },

        firstError(field) {
            return this.errors[field]?.[0] ?? null;
        },
    };
}
