<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/** Andamento de uma tarefa exibido no sino de notificações. */
class TaskProgress extends Notification
{
    /** @param  string  $kind  assigned|moved|testing|approved|rejected|done */
    public function __construct(
        private int $taskId,
        private string $taskTitle,
        private string $kind,
        private string $message,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, int|string> */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->taskId,
            'title' => $this->taskTitle,
            'kind' => $this->kind,
            'message' => $this->message,
        ];
    }
}
