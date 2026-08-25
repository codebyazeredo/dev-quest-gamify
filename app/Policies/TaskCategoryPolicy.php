<?php

namespace App\Policies;

use App\Models\TaskCategory;
use App\Models\User;

class TaskCategoryPolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, TaskCategory $category): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, TaskCategory $category): bool
    {
        return $user->isAdmin();
    }
}
