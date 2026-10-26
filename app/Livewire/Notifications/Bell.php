<?php

namespace App\Livewire\Notifications;

use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Bell extends Component
{
    /** rail: abre ao lado da barra lateral fina; top: abre abaixo da barra superior (celular). */
    public string $placement = 'rail';

    public function openNotification(string $notificationId): mixed
    {
        $taskId = app(NotificationService::class)->markAsRead(auth()->user(), $notificationId);

        return $taskId !== null ? $this->redirectRoute('tasks.show', $taskId) : null;
    }

    public function markAllAsRead(): void
    {
        app(NotificationService::class)->markAllAsRead(auth()->user());
    }

    public function render(): View
    {
        $service = app(NotificationService::class);
        $user = auth()->user();

        return view('livewire.notifications.bell', [
            'notifications' => $service->latestFor($user),
            'unreadCount' => $service->unreadCountFor($user),
        ]);
    }
}
