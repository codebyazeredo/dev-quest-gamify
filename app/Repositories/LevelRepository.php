<?php

namespace App\Repositories;

use App\Models\Level;

class LevelRepository extends Repository
{
    protected function model(): string
    {
        return Level::class;
    }

    public function highestReachedWith(int $totalXp): Level
    {
        return $this->query()
            ->where('xp_required', '<=', max(0, $totalXp))
            ->orderByDesc('level')
            ->firstOrFail();
    }

    public function highest(): Level
    {
        return $this->query()->orderByDesc('level')->firstOrFail();
    }

    public function after(Level $current): ?Level
    {
        return $this->query()->where('level', '>', $current->level)->orderBy('level')->first();
    }
}
