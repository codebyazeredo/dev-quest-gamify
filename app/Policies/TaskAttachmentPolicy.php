<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;

class TaskAttachmentPolicy
{
    public function view(User $user, TaskAttachment $attachment): bool
    {
        return true;
    }

    public function create(User $user, Task $task): bool
    {
        return ! $task->isArchived();
    }

    public function delete(User $user, TaskAttachment $attachment): bool
    {
        return $user->isAdmin() || $user->isProductOwner() || $attachment->user_id === $user->id;
    }
}
