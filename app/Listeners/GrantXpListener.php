<?php

namespace App\Listeners;

use App\Enums\XpSourceType;
use App\Events\TaskEventCreated;
use App\Repositories\TaskEventRuleRepository;
use App\Services\XpService;

class GrantXpListener
{
    public function __construct(
        private XpService $xpService,
        private TaskEventRuleRepository $eventRules,
    ) {}

    public function handle(TaskEventCreated $event): void
    {
        $taskEvent = $event->taskEvent;

        if ($taskEvent->type->isPercentageBased()) {

            return;
        }

        $rule = $this->eventRules->findByType($taskEvent->type);

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
            "{$taskEvent->type->label()} - Tarefa #{$task->id}",
        );
    }
}
