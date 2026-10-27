@props(['placement' => 'rail'])

<div x-data="notificationBell" x-on:click.outside="open = false" class="position-relative">
    <button type="button" x-on:click="open = ! open" class="btn btn-ghost btn-icon position-relative" data-bs-toggle="tooltip" data-bs-title="Notificações" aria-label="Notificações" aria-haspopup="true" x-bind:aria-expanded="open">
        <i class="bi" x-bind:class="unreadCount > 0 ? 'bi-bell-fill' : 'bi-bell'" aria-hidden="true"></i>

        <span x-show="unreadCount > 0" x-cloak x-text="unreadCount > 99 ? '99+' : unreadCount" class="notification-badge badge rounded-pill text-bg-danger"></span>
    </button>

    <div x-show="open" x-cloak x-transition.opacity class="dropdown-menu show notification-menu notification-menu--{{ $placement }} p-0 shadow">
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
            <span class="fw-semibold">Andamento das tarefas</span>

            <button type="button" x-show="unreadCount > 0" x-on:click="markAllAsRead" class="btn btn-link p-0 text-decoration-none" data-bs-toggle="tooltip" data-bs-title="Marcar todas como lidas" aria-label="Marcar todas como lidas"><i class="bi bi-check2-all fs-5" aria-hidden="true"></i></button>
        </div>

        <div class="notification-list">
            <template x-for="notification in notifications" :key="notification.id">
                <button type="button" x-on:click="openNotification(notification)"
                    class="dropdown-item d-flex align-items-start gap-3 px-3 py-2 text-wrap"
                    x-bind:class="{ 'bg-primary-subtle': ! notification.is_read }">
                    <span class="icon-chip icon-chip-sm" x-bind:class="`bg-${kindOf(notification).color}-subtle text-${kindOf(notification).color}-emphasis`">
                        <i class="bi" x-bind:class="kindOf(notification).icon" aria-hidden="true"></i>
                    </span>

                    <span class="min-w-0 flex-grow-1">
                        <span class="d-block fw-semibold text-truncate" x-text="notification.title"></span>
                        <span class="d-block small text-body-secondary" x-text="notification.message"></span>
                        <span class="d-block small text-body-tertiary mt-1" x-text="notification.created_at_human"></span>
                    </span>
                </button>
            </template>

            <div x-show="notifications.length === 0" x-cloak class="text-center text-body-secondary py-5 px-3">
                <i class="bi bi-bell-slash fs-3 d-block mb-2" aria-hidden="true"></i>
                Nenhuma notificação por enquanto.
            </div>
        </div>
    </div>
</div>
