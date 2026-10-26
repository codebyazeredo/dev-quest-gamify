@php
    $kinds = [
        'assigned' => ['icon' => 'bi-person-plus', 'color' => 'primary'],
        'moved' => ['icon' => 'bi-arrow-left-right', 'color' => 'secondary'],
        'testing' => ['icon' => 'bi-clipboard2-check', 'color' => 'info'],
        'approved' => ['icon' => 'bi-check-circle', 'color' => 'success'],
        'rejected' => ['icon' => 'bi-x-circle', 'color' => 'danger'],
        'done' => ['icon' => 'bi-flag', 'color' => 'success'],
    ];
@endphp

<div wire:poll.20s x-data="{ open: false }" x-on:click.outside="open = false" class="position-relative">
    <button type="button" x-on:click="open = ! open" class="{{ $placement === 'rail' ? 'rail-item' : 'rail-item border-0 bg-transparent text-reset' }} position-relative" aria-label="Notificações" aria-haspopup="true" x-bind:aria-expanded="open">
        <i class="bi {{ $unreadCount > 0 ? 'bi-bell-fill' : 'bi-bell' }}" aria-hidden="true"></i>

        @if ($unreadCount > 0)
            <span class="notification-badge badge rounded-pill text-bg-danger">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </button>

    <div x-show="open" x-cloak x-transition.opacity class="dropdown-menu show notification-menu notification-menu--{{ $placement }} p-0 shadow">
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
            <span class="fw-semibold">Andamento das tarefas</span>

            @if ($unreadCount > 0)
                <button type="button" wire:click="markAllAsRead" class="btn btn-link p-0 text-decoration-none" data-bs-toggle="tooltip" data-bs-title="Marcar todas como lidas" aria-label="Marcar todas como lidas"><i class="bi bi-check2-all fs-5" aria-hidden="true"></i></button>
            @endif
        </div>

        <div class="notification-list">
            @forelse ($notifications as $notification)
                @php $kind = $kinds[$notification->data['kind'] ?? 'moved'] ?? $kinds['moved']; @endphp

                <button type="button" wire:click="openNotification('{{ $notification->id }}')" wire:key="notification-{{ $notification->id }}"
                    class="dropdown-item d-flex align-items-start gap-3 px-3 py-2 text-wrap {{ $notification->read_at === null ? 'bg-primary-subtle' : '' }}">
                    <span class="icon-chip icon-chip-sm bg-{{ $kind['color'] }}-subtle text-{{ $kind['color'] }}-emphasis">
                        <i class="bi {{ $kind['icon'] }}" aria-hidden="true"></i>
                    </span>

                    <span class="min-w-0 flex-grow-1">
                        <span class="d-block fw-semibold text-truncate">{{ $notification->data['title'] ?? 'Tarefa' }}</span>
                        <span class="d-block small text-body-secondary">{{ $notification->data['message'] ?? '' }}</span>
                        <span class="d-block small text-body-tertiary mt-1">{{ $notification->created_at->diffForHumans() }}</span>
                    </span>
                </button>
            @empty
                <div class="text-center text-body-secondary py-5 px-3">
                    <i class="bi bi-bell-slash fs-3 d-block mb-2" aria-hidden="true"></i>
                    Nenhuma notificação por enquanto.
                </div>
            @endforelse
        </div>
    </div>
</div>
