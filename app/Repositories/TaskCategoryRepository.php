<?php

namespace App\Repositories;

use App\Models\TaskCategory;
use Illuminate\Database\Eloquent\Builder;

class TaskCategoryRepository extends Repository
{
    protected function model(): string
    {
        return TaskCategory::class;
    }

    protected function query(): Builder
    {
        return parent::query()->orderBy('name');
    }

    public function hasTasks(TaskCategory $category): bool
    {
        return $category->tasks()->exists();
    }
}
