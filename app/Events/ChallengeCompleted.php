<?php

namespace App\Events;

use App\Models\Challenge;
use App\Models\User;

class ChallengeCompleted
{
    public function __construct(
        public readonly User $user,
        public readonly Challenge $challenge,
    ) {}
}
