<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TaskChecklistItem;
use App\Models\User;

class TaskChecklistItemPolicy
{
    public function create(User $user, Task $task): bool
    {
        return ! $task->isArchived();
    }

    public function update(User $user, TaskChecklistItem $item): bool
    {
        return ! $item->task->isArchived();
    }

    public function delete(User $user, TaskChecklistItem $item): bool
    {
        return ! $item->task->isArchived();
    }
}
