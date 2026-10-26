<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserAchievement;

class UserAchievementRepository extends Repository
{
    protected function model(): string
    {
        return UserAchievement::class;
    }

    /** @return array<int, int> */
    public function unlockedIdsFor(User $user): array
    {
        return $this->query()->where('user_id', $user->id)->pluck('achievement_id')->all();
    }

    public function isUnlocked(User $user, int $achievementId): bool
    {
        return $this->query()->where('user_id', $user->id)->where('achievement_id', $achievementId)->exists();
    }

    public function unlock(User $user, int $achievementId): UserAchievement
    {
        return $this->create(['user_id' => $user->id, 'achievement_id' => $achievementId, 'unlocked_at' => now()]);
    }

    public function countFor(User $user): int
    {
        return $this->query()->where('user_id', $user->id)->count();
    }
}
