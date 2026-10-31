<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreChecklistItemRequest;
use App\Models\Task;
use App\Models\TaskChecklistItem;
use App\Services\TaskChecklistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskChecklistItemController extends Controller
{
    public function __construct(private TaskChecklistService $checklist) {}

    public function store(StoreChecklistItemRequest $request, Task $task): JsonResponse
    {
        $this->authorize('view', $task);
        $this->authorize('create', [TaskChecklistItem::class, $task]);

        $this->checklist->add($task, $request->validated('title'));

        return response()->json(['message' => 'Item adicionado ao checklist.'], 201);
    }

    public function toggle(Request $request, TaskChecklistItem $item): JsonResponse
    {
        $this->authorize('update', $item);

        $item = $this->checklist->toggle($item, $request->user());

        return response()->json(['message' => $item->isCompleted() ? 'Item concluído.' : 'Item reaberto.']);
    }

    public function destroy(TaskChecklistItem $item): JsonResponse
    {
        $this->authorize('delete', $item);

        $this->checklist->delete($item);

        return response()->json(['message' => 'Item removido do checklist.']);
    }
}
