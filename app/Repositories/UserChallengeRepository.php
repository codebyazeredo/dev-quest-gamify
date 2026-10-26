<?php

namespace App\Repositories;

use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use Illuminate\Database\Eloquent\Builder;

class UserChallengeRepository extends Repository
{
    protected function model(): string
    {
        return UserChallenge::class;
    }

    protected function completed(): Builder
    {
        return $this->query()->whereNotNull('completed_at');
    }

    public function countCompletedBy(User $user): int
    {
        return $this->completed()->where('user_id', $user->id)->count();
    }

    /** @return array<int, int> */
    public function userIdsWhoCompleted(Challenge $challenge): array
    {
        return $this->completed()->where('challenge_id', $challenge->id)->pluck('user_id')->all();
    }

    /** @return array<int, int> */
    public function userIdsWithAtLeastCompleted(int $quantity): array
    {
        return $this->completed()
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) >= ?', [$quantity])
            ->pluck('user_id')
            ->all();
    }
}
