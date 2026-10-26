<?php

namespace App\Repositories;

use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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

    public function firstOrCreateFor(User $user, Challenge $challenge): UserChallenge
    {
        return $this->query()->firstOrCreate(
            ['user_id' => $user->id, 'challenge_id' => $challenge->id],
            ['progress' => 0],
        );
    }

    public function incrementProgress(UserChallenge $userChallenge, int $amount): UserChallenge
    {
        $userChallenge->increment('progress', $amount);

        return $userChallenge;
    }

    public function markCompleted(UserChallenge $userChallenge): UserChallenge
    {
        return $this->update($userChallenge, ['completed_at' => now()]);
    }

    /** Progresso do usuário indexado pelo id do desafio. */
    public function keyedByChallengeFor(User $user): Collection
    {
        return $this->query()->where('user_id', $user->id)->get()->keyBy('challenge_id');
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
