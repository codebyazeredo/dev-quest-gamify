<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskCommentRequest;
use App\Models\Task;
use App\Models\TaskComment;
use App\Services\TaskCommentService;
use Illuminate\Http\JsonResponse;

class TaskCommentController extends Controller
{
    public function __construct(private TaskCommentService $comments) {}

    public function store(StoreTaskCommentRequest $request, Task $task): JsonResponse
    {
        $this->authorize('view', $task);
        $this->authorize('create', [TaskComment::class, $task]);

        $this->comments->add($task, $request->user(), $request->validated('body'));

        return response()->json(['message' => 'Comentário adicionado.'], 201);
    }

    public function destroy(TaskComment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $this->comments->delete($comment);

        return response()->json(['message' => 'Comentário removido.']);
    }
}
