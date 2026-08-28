<?php

namespace App\Policies;

use App\Enums\TaskStatus;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isProductOwner() || $user->isSuporte();
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->isAdmin() || $user->isProductOwner()) {
            return true;
        }

        if ($user->isSuporte()) {
            return in_array($task->status, [null, TaskStatus::BACKLOG], true);
        }

        return false;
    }

    public function assignAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function claim(User $user, Task $task): bool
    {
        return $user->isDeveloper() && $task->assigned_to === null;
    }

    public function move(User $user, Task $task, BoardColumn $destination): bool
    {
        if ($destination->status === TaskStatus::APPROVED) {
            return false;
        }

        if ($destination->status === TaskStatus::DONE) {
            return false;
        }

        if ($task->status === TaskStatus::TESTING && $destination->status !== TaskStatus::TESTING) {
            return false;
        }

        if ($user->isAdmin() || $user->isProductOwner()) {
            return true;
        }

        if (! $user->isDeveloper()) {
            return false;
        }

        $isSignOff = ($task->status?->value ?? 0) < TaskStatus::TESTING->value
            && $destination->status === TaskStatus::TESTING;

        if ($isSignOff) {
            return $task->assigned_to !== $user->id;
        }

        return $task->assigned_to === $user->id;
    }

    public function approve(User $user, Task $task): bool
    {
        return $this->canApproveOrReject($user, $task);
    }

    public function reject(User $user, Task $task): bool
    {
        return $this->canApproveOrReject($user, $task);
    }

    private function canApproveOrReject(User $user, Task $task): bool
    {
        if ($user->isAdmin() || $user->isProductOwner()) {
            return true;
        }

        return $user->isTester() && $task->assigned_to !== $user->id;
    }

    public function markHomologationCompleted(User $user, Task $task): bool
    {
        return $task->status === TaskStatus::APPROVED && $this->canActOnOwnTask($user, $task);
    }

    public function markDeployed(User $user, Task $task): bool
    {
        return $task->status === TaskStatus::DONE && $this->canActOnOwnTask($user, $task);
    }

    private function canActOnOwnTask(User $user, Task $task): bool
    {
        if ($user->isAdmin() || $user->isProductOwner()) {
            return true;
        }

        return $user->isDeveloper() && $task->assigned_to === $user->id;
    }

    public function archive(User $user, Task $task): bool
    {
        return $user->isAdmin() || $user->isProductOwner();
    }

    public function unarchive(User $user, Task $task): bool
    {
        return $user->isAdmin() || $user->isProductOwner();
    }
}
