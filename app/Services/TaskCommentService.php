<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use App\Repositories\TaskCommentRepository;

class TaskCommentService
{
    public function __construct(
        private TaskCommentRepository $comments,
        private TaskProgressNotificationService $notifications,
    ) {}

    public function add(Task $task, User $author, string $body): TaskComment
    {
        $comment = $this->comments->create([
            'task_id' => $task->id,
            'user_id' => $author->id,
            'body' => $body,
        ]);

        $this->notifications->notifyCommented($task, $author, $body);

        return $comment;
    }

    public function delete(TaskComment $comment): void
    {
        $this->comments->delete($comment);
    }
}
