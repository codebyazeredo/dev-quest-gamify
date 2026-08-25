<?php

namespace App\Listeners;

use App\Enums\TaskEventType;
use App\Events\TaskCompleted;
use App\Events\TaskEventCreated;
use App\Services\AchievementService;

class AchievementListener
{
    public function __construct(private AchievementService $achievementService) {}

    public function handleTaskCompleted(TaskCompleted $event): void
    {
        $assignee = $event->task->assignedTo;

        if ($assignee !== null) {
            $this->achievementService->evaluateForUser($assignee);
        }
    }

    public function handleTaskEventCreated(TaskEventCreated $event): void
    {
        if ($event->taskEvent->type !== TaskEventType::DEPLOYED) {
            return;
        }

        $assignee = $event->taskEvent->task->assignedTo;

        if ($assignee !== null) {
            $this->achievementService->evaluateForUser($assignee);
        }
    }
}
