<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use App\Events\TaskMoved;
use App\Services\TaskProgressNotificationService;

class TaskProgressNotificationListener
{
    public function __construct(private TaskProgressNotificationService $notifications) {}

    public function handleTaskMoved(TaskMoved $event): void
    {
        $this->notifications->notifyMoved($event->task, $event->to, $event->actor);
    }

    public function handleTaskAssigned(TaskAssigned $event): void
    {
        $this->notifications->notifyAssigned($event->task, $event->assignee, $event->actor);
    }
}
