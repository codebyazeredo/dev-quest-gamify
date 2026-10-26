<?php

namespace App\Repositories;

use App\Models\UserTitle;

class UserTitleRepository extends Repository
{
    protected function model(): string
    {
        return UserTitle::class;
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
