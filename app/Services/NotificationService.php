<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\NotificationRepository;
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

    public function markAsRead(User $user, string $notificationId): ?int
    {
        $notification = $this->notifications->findFor($user, $notificationId);

        if ($notification === null) {
            return null;
        }

        $notification->markAsRead();

        return $notification->data['task_id'] ?? null;
    }

    public function markAllAsRead(User $user): void
    {
        $this->notifications->markAllReadFor($user);
    }
}
