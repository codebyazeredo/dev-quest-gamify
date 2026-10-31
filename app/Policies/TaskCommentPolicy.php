<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;

class TaskCommentPolicy
{
    public function create(User $user, Task $task): bool
    {
        return ! $task->isArchived();
    }

    public function delete(User $user, TaskComment $comment): bool
    {
        return $user->isAdmin() || $user->isProductOwner() || $comment->user_id === $user->id;
    }
}
