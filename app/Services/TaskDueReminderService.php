<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;

class TaskDueReminderService
{
    public const REMINDER_WINDOW_HOURS = 24;

    public function __construct(
        private TaskRepository $tasks,
        private TaskProgressNotificationService $notifications,
    ) {}

    public function sendPending(): int
    {
        $dueTasks = $this->tasks->assignedDueBeforeWithoutReminder(now()->addHours(self::REMINDER_WINDOW_HOURS));

        $dueTasks->each(function (Task $task) {
            $this->notifications->notifyDueSoon($task);
            $this->tasks->update($task, ['due_reminder_sent_at' => now()]);
        });

        return $dueTasks->count();
    }
}
