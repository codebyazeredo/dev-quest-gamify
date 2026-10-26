<?php

namespace App\Services\Admin;

use App\Enums\TaskEventType;
use App\Exceptions\DuplicateEntryException;
use App\Models\TaskEventRule;
use App\Repositories\TaskEventRuleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class EventRuleService
{
    public function __construct(private TaskEventRuleRepository $rules) {}

    /** @return Collection<int, array{type: TaskEventType, rule: ?TaskEventRule}> */
    public function overview(): Collection
    {
        return collect(TaskEventType::cases())->map(fn (TaskEventType $type) => [
            'type' => $type,
            'rule' => $this->rules->findByType($type),
        ]);
    }

    public function findOrFail(int $typeValue): TaskEventRule
    {
        $type = TaskEventType::tryFrom($typeValue) ?? throw new ModelNotFoundException;

        return $this->rules->findByTypeOrFail($type);
    }

    public function create(TaskEventType $type, int $xpReward, bool $active): TaskEventRule
    {
        if ($this->rules->existsForType($type)) {
            throw new DuplicateEntryException('Este evento já possui uma regra configurada.');
        }

        return $this->rules->create([
            'type' => $type,
            'xp_reward' => $xpReward,
            'active' => $active,
        ]);
    }

    public function update(TaskEventRule $rule, int $xpReward, bool $active): TaskEventRule
    {
        return $this->rules->update($rule, [
            'xp_reward' => $xpReward,
            'active' => $active,
        ]);
    }
}
