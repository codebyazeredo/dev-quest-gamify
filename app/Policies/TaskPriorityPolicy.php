<?php

namespace App\Policies;

use App\Models\TaskPriority;
use App\Models\User;

class TaskPriorityPolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, TaskPriority $priority): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, TaskPriority $priority): bool
    {
        return $user->isAdmin();
    }
}
