<?php

namespace App\Listeners;

use App\Enums\ChallengeType;
use App\Events\TaskCompleted;
use App\Services\ChallengeService;

class ChallengeListener
{
    public function __construct(private ChallengeService $challengeService) {}

    public function handleTaskCompleted(TaskCompleted $event): void
    {
        $task = $event->task;
        $assignee = $task->assignedTo;

        if ($assignee === null) {
            return;
        }

        $occurredAt = $task->completed_at ?? now();

        $this->challengeService->recordProgress($assignee, ChallengeType::TASKS_COMPLETED, $occurredAt);

        if ($task->category->slug === 'bug') {
            $this->challengeService->recordProgress($assignee, ChallengeType::BUGS_RESOLVED, $occurredAt);
        }
    }
}
