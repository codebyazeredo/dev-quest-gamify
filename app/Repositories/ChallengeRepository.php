<?php

namespace App\Repositories;

use App\Models\Challenge;
use Illuminate\Database\Eloquent\Builder;

class ChallengeRepository extends Repository
{
    protected function model(): string
    {
        return Challenge::class;
    }

    protected function query(): Builder
    {
        return parent::query()->orderByDesc('starts_at');
    }

    public function hasProgress(Challenge $challenge): bool
    {
        return $challenge->userChallenges()->exists();
    }
}
