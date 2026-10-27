<?php

namespace App\Repositories;

use App\Enums\TaskEventType;
use App\Models\Task;
use App\Models\TaskEvent;
use App\Models\User;

class TaskEventRepository extends Repository
{
    protected function model(): string
    {
        return TaskEvent::class;
    }

    public function existsOfType(Task $task, TaskEventType $type): bool
    {
        return $this->query()->where('task_id', $task->id)->where('type', $type)->exists();
    }

    public function firstOfType(Task $task, TaskEventType $type): ?TaskEvent
    {
        return $this->query()->where('task_id', $task->id)->where('type', $type)->first();
    }

    public function countDeploysOfTasksAssignedTo(User $user): int
    {
        return $this->query()
            ->where('type', TaskEventType::DEPLOYED)
            ->whereHas('task', fn ($query) => $query->where('assigned_to', $user->id))
            ->count();
    }
}
