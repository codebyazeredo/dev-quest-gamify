<?php

namespace App\Services;

use App\Enums\TaskEventType;
use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use App\Repositories\XpTransactionRepository;

class TaskDetailService
{
    public function __construct(
        private TaskRepository $tasks,
        private AssignableUserService $assignableUsers,
        private XpTransactionRepository $xpTransactions,
    ) {}

    /** @return array<string, mixed> */
    public function detailsFor(Task $task, User $viewer): array
    {
        $this->tasks->loadDetails($task);

        return [
            'task' => $task,
            'developers' => $viewer->isAdmin() ? $this->assignableUsers->all() : collect(),
            'completionBonus' => $this->xpTransactions->findTaskCompletionBonus($task),
            'hasHomologation' => $this->hasEvent($task, TaskEventType::HOMOLOGATION_COMPLETED),
            'hasDeployed' => $this->hasEvent($task, TaskEventType::DEPLOYED),
        ];
    }

    private function hasEvent(Task $task, TaskEventType $type): bool
    {
        return $task->taskEvents->contains(fn ($event) => $event->type === $type);
    }
}
