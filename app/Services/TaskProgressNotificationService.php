<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedMail;
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

        if ($assignee->id !== $actor->id) {
            $assignee->notify(new TaskAssignedMail($task, $actor));
        }
    }

    public function notifyDueSoon(Task $task): void
    {
        $message = $task->isOverdue()
            ? 'O prazo desta tarefa já venceu.'
            : "O prazo desta tarefa vence em {$task->due_at->format('d/m/Y H:i')}.";

        $task->assignedTo->notify(new TaskProgress($task->id, $task->title, 'reminder', $message));
    }

    public function notifyCommented(Task $task, User $author, string $body): void
    {
        $this->send(
            collect([$task->assignedTo, $task->createdBy]),
            $author,
            $task,
            'commented',
            "{$author->name} comentou: ".str($body)->limit(80),
        );
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
