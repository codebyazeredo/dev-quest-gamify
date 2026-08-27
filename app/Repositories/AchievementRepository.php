<?php

namespace App\Repositories;

use App\Models\Achievement;
use Illuminate\Database\Eloquent\Builder;

class AchievementRepository extends Repository
{
    protected function model(): string
    {
        return Achievement::class;
    }

    protected function query(): Builder
    {
        return parent::query()->orderBy('name');
    }

    public function hasUnlocks(Achievement $achievement): bool
    {
        return $achievement->userAchievements()->exists();
    }
}
