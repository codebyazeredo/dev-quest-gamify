<?php

namespace App\Repositories;

use App\Enums\TaskEventType;
use App\Models\TaskEventRule;
use Illuminate\Support\Collection;

class TaskEventRuleRepository extends Repository
{
    protected function model(): string
    {
        return TaskEventRule::class;
    }

    public function findByType(TaskEventType $type): ?TaskEventRule
    {
        return $this->query()->where('type', $type)->first();
    }

    public function findByTypeOrFail(TaskEventType $type): TaskEventRule
    {
        return $this->query()->where('type', $type)->firstOrFail();
    }

    public function existsForType(TaskEventType $type): bool
    {
        return $this->query()->where('type', $type)->exists();
    }

    public function configuredTypes(): Collection
    {
        return $this->query()->pluck('type');
    }
}
