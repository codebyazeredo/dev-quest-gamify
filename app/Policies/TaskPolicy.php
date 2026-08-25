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

    /**
     * A move that takes the task from before Testing to Testing-or-beyond is the
     * review sign-off (see §9: developer "não pode revisar a própria tarefa") —
     * the assignee cannot perform it themselves; any other developer can.
     */
    public function move(User $user, Task $task, BoardColumn $destination): bool
    {
        if ($user->isAdmin() || $user->isProductOwner()) {
            return true;
        }

        if (! $user->isDeveloper()) {
            return false;
        }

        $isSignOff = $task->status->value < TaskStatus::TESTING->value
            && $destination->status->value >= TaskStatus::TESTING->value;

        if ($isSignOff) {
            return $task->assigned_to !== $user->id;
        }

        return $task->assigned_to === $user->id;
    }

    public function markHomologationCompleted(User $user, Task $task): bool
    {
        return $this->canActOnOwnTask($user, $task);
    }

    public function markDeployed(User $user, Task $task): bool
    {
        return $this->canActOnOwnTask($user, $task);
    }

    private function canActOnOwnTask(User $user, Task $task): bool
    {
        if ($user->isAdmin() || $user->isProductOwner()) {
            return true;
        }

        return $user->isDeveloper() && $task->assigned_to === $user->id;
    }
}
