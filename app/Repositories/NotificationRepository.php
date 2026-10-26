<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationRepository extends Repository
{
    protected function model(): string
    {
        return DatabaseNotification::class;
    }

    public function latestFor(User $user, int $limit): Collection
    {
        return $user->notifications()->limit($limit)->get();
    }

    public function unreadCountFor(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    public function findFor(User $user, string $id): ?DatabaseNotification
    {
        return $user->notifications()->whereKey($id)->first();
    }

    public function markAllReadFor(User $user): void
    {
        $user->unreadNotifications()->update(['read_at' => now()]);
    }
}
