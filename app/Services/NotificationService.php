<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\NotificationRepository;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    public function __construct(private NotificationRepository $notifications) {}

    public function latestFor(User $user, int $limit = 12): Collection
    {
        return $this->notifications->latestFor($user, $limit);
    }

    public function unreadCountFor(User $user): int
    {
        return $this->notifications->unreadCountFor($user);
    }

    public function markAsRead(User $user, string $notificationId): DatabaseNotification
    {
        $notification = $this->notifications->findForOrFail($user, $notificationId);

        $notification->markAsRead();

        return $notification;
    }

    public function markAllAsRead(User $user): void
    {
        $this->notifications->markAllReadFor($user);
    }
}
