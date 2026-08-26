<?php

namespace App\Services;

use App\Models\Level;
use App\Models\User;
use App\Models\XpTransaction;

class LevelService
{
    public function totalXpFor(User $user): int
    {
        return (int) XpTransaction::where('user_id', $user->id)->sum('amount');
    }

    public function levelForTotalXp(int $totalXp): Level
    {
        return Level::where('xp_required', '<=', max(0, $totalXp))
            ->orderByDesc('level')
            ->firstOrFail();
    }

    /**
     * Admins are the game's GM — always shown at max level, regardless of
     * their actual XP total (which is left untouched, no fake XP is granted).
     */
    public function currentLevelFor(User $user): Level
    {
        if ($user->isAdmin()) {
            return $this->maxLevel();
        }

        return $this->levelForTotalXp($this->totalXpFor($user));
    }

    public function maxLevel(): Level
    {
        return Level::orderByDesc('level')->firstOrFail();
    }

    public function nextLevelFor(Level $current): ?Level
    {
        return Level::where('level', '>', $current->level)->orderBy('level')->first();
    }
}
