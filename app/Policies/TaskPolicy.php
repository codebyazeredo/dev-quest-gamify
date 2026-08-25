<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isProductOwner();
    }

    public function update(User $user, Task $task): bool
    {
        return $user->isAdmin() || $user->isProductOwner();
    }

    public function assignAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function claim(User $user, Task $task): bool
    {
        return $user->isDeveloper() && $task->assigned_to === null;
    }

    public function move(User $user, Task $task): bool
    {
        if ($user->isAdmin() || $user->isProductOwner()) {
            return true;
        }

        return $user->isDeveloper() && $task->assigned_to === $user->id;
    }

    public function markHomologationCompleted(User $user, Task $task): bool
    {
        return $this->move($user, $task);
    }

    public function markDeployed(User $user, Task $task): bool
    {
        return $this->move($user, $task);
    }
}
