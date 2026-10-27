<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskAttachmentsRequest;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Services\TaskAttachmentService;
use Illuminate\Http\JsonResponse;

class TaskAttachmentController extends Controller
{
    public function __construct(private TaskAttachmentService $attachments) {}

    public function store(StoreTaskAttachmentsRequest $request, Task $task): JsonResponse
    {
        $this->authorize('create', [TaskAttachment::class, $task]);

        $created = $this->attachments->store($task, $request->user(), $request->file('files'));

        $message = $created->count() === 1 ? 'Anexo adicionado.' : "{$created->count()} anexos adicionados.";

        return response()->json(['message' => $message], 201);
    }

    public function destroy(TaskAttachment $attachment): JsonResponse
    {
        $this->authorize('delete', $attachment);

        $this->attachments->delete($attachment);

        return response()->json(['message' => "Anexo \"{$attachment->original_name}\" removido."]);
    }
}
