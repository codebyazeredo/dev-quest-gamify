<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskProgress;
use App\Repositories\UserRepository;

class TaskProgressNotificationService
{
    public function __construct(private UserRepository $users) {}

    public function notifyMoved(Task $task, BoardColumn $destination, User $actor): void
    {
        [$kind, $message] = $this->describeMove($task, $destination, $actor);

        $recipients = collect([$task->assignedTo, $task->createdBy]);

        if ($destination->status === TaskStatus::TESTING) {
            $recipients = $recipients->merge($this->users->withRole('tester'));
        }

        $this->send($recipients, $actor, $task, $kind, $message);
    }

    public function notifyAssigned(Task $task, User $assignee, User $actor): void
    {
        $this->send(collect([$assignee]), $actor, $task, 'assigned', "{$actor->name} atribuiu esta tarefa a você.");
    }

    /** @return array{0: string, 1: string} */
    private function describeMove(Task $task, BoardColumn $destination, User $actor): array
    {
        return match (true) {
            $destination->status === TaskStatus::APPROVED => ['approved', "{$actor->name} aprovou a tarefa."],
            $destination->status === TaskStatus::DONE => ['done', 'A tarefa foi concluída.'],
            $destination->status === TaskStatus::TESTING => ['testing', "Pronta para teste: {$actor->name} moveu para {$destination->name}."],
            $destination->status === TaskStatus::TODO && $task->rejection_reason !== null => ['rejected', "{$actor->name} reprovou a tarefa: {$task->rejection_reason}"],
            default => ['moved', "{$actor->name} moveu para {$destination->name}."],
        };
    }

    private function send($recipients, User $actor, Task $task, string $kind, string $message): void
    {
        $recipients
            ->filter()
            ->unique('id')
            ->reject(fn (User $recipient) => $recipient->id === $actor->id)
            ->each(fn (User $recipient) => $recipient->notify(new TaskProgress($task->id, $task->title, $kind, $message)));
    }
}
