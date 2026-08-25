<?php

namespace App\Events;

use App\Models\User;

class StreakBonusEarned
{
    public function __construct(
        public readonly User $user,
        public readonly int $streakCount,
        public readonly int $xpAwarded,
    ) {}
}
