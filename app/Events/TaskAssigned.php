<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;

class TaskAssigned
{
    public function __construct(
        public readonly Task $task,
        public readonly User $assignee,
        public readonly User $actor,
    ) {}
}
