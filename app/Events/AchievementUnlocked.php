<?php

namespace App\Events;

use App\Models\Achievement;
use App\Models\User;

class AchievementUnlocked
{
    public function __construct(
        public readonly User $user,
        public readonly Achievement $achievement,
    ) {}
}
