<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserTitle;
use Illuminate\Support\Collection;

class UserTitleRepository extends Repository
{
    protected function model(): string
    {
        return UserTitle::class;
    }

    /** Títulos que o usuário já desbloqueou. */
    public function titlesUnlockedBy(User $user): Collection
    {
        return $this->query()->where('user_id', $user->id)->with('title')->get()->pluck('title');
    }

    public function countFor(User $user): int
    {
        return $this->query()->where('user_id', $user->id)->count();
    }

    public function grant(int $userId, int $titleId): bool
    {
        $userTitle = $this->query()->firstOrCreate(
            ['user_id' => $userId, 'title_id' => $titleId],
            ['unlocked_at' => now()],
        );

        return $userTitle->wasRecentlyCreated;
    }
}
