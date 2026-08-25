<?php

namespace App\Policies;

use App\Models\Challenge;
use App\Models\User;

class ChallengePolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Challenge $challenge): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Challenge $challenge): bool
    {
        return $user->isAdmin();
    }
}
