<?php

namespace App\Policies;

use App\Models\BusinessSector;
use App\Models\User;

class BusinessSectorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, BusinessSector $sector): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, BusinessSector $sector): bool
    {
        return $user->isAdmin();
    }
}
