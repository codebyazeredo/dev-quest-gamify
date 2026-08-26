<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\User;

class PersonPolicy
{
    public function create(User $user): bool
    {
        return $user->can('manage-people');
    }

    public function update(User $user, Person $person): bool
    {
        return $user->can('manage-people');
    }

    public function delete(User $user, Person $person): bool
    {
        return $user->can('manage-people');
    }
}
