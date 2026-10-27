const JSON_HEADERS = {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
};

export class ApiError extends Error {
    constructor(message, status, errors = {}) {
        super(message);
        this.status = status;
        this.errors = errors;
    }
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

export function showToast(toast) {
    window.dispatchEvent(new CustomEvent('toast', { detail: { toast } }));
}

function buildUrl(url, query) {
    const queryString = new URLSearchParams(query).toString();

    return queryString ? `${url}?${queryString}` : url;
}

function prepareRequest(method, body) {
    const headers = { ...JSON_HEADERS, 'X-CSRF-TOKEN': csrfToken() };

    if (body === null) {
        return { method, headers, body: undefined };
    }

    if (!(body instanceof FormData)) {
        return { method, headers, body: JSON.stringify(body) };
    }

    delete headers['Content-Type'];

    if (method !== 'POST') {
        body.append('_method', method);
    }

    return { method: 'POST', headers, body };
}

async function request(method, url, { query = {}, body = null, silent = false } = {}) {
    const response = await fetch(buildUrl(url, query), {
        credentials: 'same-origin',
        ...prepareRequest(method, body),
    });

    const payload = await response.json().catch(() => ({}));

    if (!silent) {
        (payload.toasts ?? []).forEach(showToast);
    }

    if (!response.ok) {
        if (response.status === 401) {
            window.location.assign('/login');
        }

        showToast({ type: 'error', title: payload.message ?? response.statusText, message: '' });

        throw new ApiError(payload.message ?? response.statusText, response.status, payload.errors);
    }

    if (!silent && method !== 'GET' && payload.message) {
        showToast({ type: 'success', title: payload.message, message: '' });
    }

    return payload;
}

export const api = {
    get: (url, query = {}, options = {}) => request('GET', url, { query, ...options }),
    post: (url, body = {}, options = {}) => request('POST', url, { body, ...options }),
    put: (url, body = {}, options = {}) => request('PUT', url, { body, ...options }),
    delete: (url, options = {}) => request('DELETE', url, options),
};
