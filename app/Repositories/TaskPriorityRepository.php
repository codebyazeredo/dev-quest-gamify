<?php

namespace App\Repositories;

use App\Models\TaskPriority;
use Illuminate\Database\Eloquent\Builder;

class TaskPriorityRepository extends Repository
{
    protected function model(): string
    {
        return TaskPriority::class;
    }

    protected function query(): Builder
    {
        return parent::query()->orderBy('multiplier');
    }

    public function hasTasks(TaskPriority $priority): bool
    {
        return $priority->tasks()->exists();
    }
}
