import { api } from './api';

const POLL_INTERVAL_MS = 20000;

const KINDS = {
    assigned: { icon: 'bi-person-plus', color: 'primary' },
    moved: { icon: 'bi-arrow-left-right', color: 'secondary' },
    testing: { icon: 'bi-clipboard2-check', color: 'info' },
    approved: { icon: 'bi-check-circle', color: 'success' },
    rejected: { icon: 'bi-x-circle', color: 'danger' },
    done: { icon: 'bi-flag', color: 'success' },
};

export function notificationBell() {
    return {
        open: false,
        notifications: [],
        unreadCount: 0,

        init() {
            this.load();
            setInterval(() => this.load(), POLL_INTERVAL_MS);
        },

        async load() {
            const payload = await api.get('/api/notifications', {}, { silent: true });
            this.notifications = payload.data;
            this.unreadCount = payload.unread_count;
        },

        kindOf(notification) {
            return KINDS[notification.kind] ?? KINDS.moved;
        },

        async openNotification(notification) {
            await api.post(`/api/notifications/${notification.id}/read`, {}, { silent: true });

            if (notification.task_id !== null) {
                window.location.assign(`/tasks/${notification.task_id}`);
            }
        },

        async markAllAsRead() {
            await api.post('/api/notifications/read-all', {}, { silent: true });
            await this.load();
        },
    };
}
