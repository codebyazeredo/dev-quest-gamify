<?php

namespace App\Listeners;

use App\Enums\XpSourceType;
use App\Events\TaskEventCreated;
use App\Models\TaskEventRule;
use App\Services\XpService;

class GrantXpListener
{
    public function __construct(private XpService $xpService) {}

    public function handle(TaskEventCreated $event): void
    {
        $taskEvent = $event->taskEvent;

        $rule = TaskEventRule::where('type', $taskEvent->type)->first();

        if (! $rule || ! $rule->active || $rule->xp_reward <= 0) {
            return;
        }

        $task = $taskEvent->task;
        $recipient = $task->assignedTo;

        if ($recipient === null) {
            return;
        }

        $this->xpService->grant(
            $recipient,
            $rule->xp_reward,
            XpSourceType::TASK_EVENT,
            $taskEvent->id,
            "{$taskEvent->type->label()} - Task #{$task->id}",
        );
    }
}
