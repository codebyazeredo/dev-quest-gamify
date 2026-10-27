<?php

namespace App\Services;

use App\Models\Level;
use App\Models\User;
use App\Repositories\LevelRepository;
use App\Repositories\XpTransactionRepository;

class LevelService
{
    public function __construct(
        private LevelRepository $levels,
        private XpTransactionRepository $xpTransactions,
    ) {}

    /** @return array{totalXp: int, currentLevel: Level, nextLevel: ?Level, xpIntoLevel: int, xpForNext: int, participatesInLeveling: bool} */
    public function progressFor(User $user): array
    {
        $totalXp = $this->totalXpFor($user);
        $currentLevel = $this->currentLevelFor($user);
        $nextLevel = $this->nextLevelFor($currentLevel);

        return [
            'totalXp' => $totalXp,
            'currentLevel' => $currentLevel,
            'nextLevel' => $nextLevel,
            'xpIntoLevel' => $totalXp - $currentLevel->xp_required,
            'xpForNext' => $nextLevel ? $nextLevel->xp_required - $currentLevel->xp_required : 0,
            'participatesInLeveling' => $this->participatesInLeveling($user),
        ];
    }

    public function totalXpFor(User $user): int
    {
        return $this->xpTransactions->totalFor($user);
    }

    public function levelForTotalXp(int $totalXp): Level
    {
        return $this->levels->highestReachedWith($totalXp);
    }

    public function currentLevelFor(User $user): Level
    {
        if (! $this->participatesInLeveling($user)) {
            return $this->maxLevel();
        }

        return $this->levelForTotalXp($this->totalXpFor($user));
    }

    public function participatesInLeveling(User $user): bool
    {
        return ! ($user->isAdmin() || $user->isProductOwner());
    }

    public function maxLevel(): Level
    {
        return $this->levels->highest();
    }

    public function nextLevelFor(Level $current): ?Level
    {
        return $this->levels->after($current);
    }
}
