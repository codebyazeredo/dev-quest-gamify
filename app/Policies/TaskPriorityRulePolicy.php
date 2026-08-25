<?php

namespace App\Policies;

use App\Models\TaskPriorityRule;
use App\Models\User;

class TaskPriorityRulePolicy
{
    public function update(User $user, TaskPriorityRule $rule): bool
    {
        return $user->isAdmin();
    }
}
