<?php

namespace App\Policies;

use App\Models\TaskEventRule;
use App\Models\User;

class TaskEventRulePolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, TaskEventRule $rule): bool
    {
        return $user->isAdmin();
    }
}
