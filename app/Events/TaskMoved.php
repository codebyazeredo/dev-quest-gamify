<?php

namespace App\Events;

use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;

class TaskMoved
{
    public function __construct(
        public readonly Task $task,
        public readonly BoardColumn $to,
        public readonly User $actor,
    ) {}
}
