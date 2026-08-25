<?php

namespace App\Events;

use App\Models\Level;
use App\Models\User;

class LevelUp
{
    public function __construct(
        public readonly User $user,
        public readonly Level $previousLevel,
        public readonly Level $newLevel,
    ) {}
}
