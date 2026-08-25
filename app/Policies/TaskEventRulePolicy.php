<?php

namespace App\Policies;

use App\Models\TaskEventRule;
use App\Models\User;

class TaskEventRulePolicy
{
    public function update(User $user, TaskEventRule $rule): bool
    {
        return $user->isAdmin();
    }
}
