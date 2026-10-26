<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TaskPriorityRequest;
use App\Http\Requests\PaginatedListRequest;
use App\Http\Resources\TaskPriorityResource;
use App\Models\TaskPriority;
use App\Services\Admin\PriorityService;
use Illuminate\Http\JsonResponse;

class TaskPriorityController extends Controller
{
    public function __construct(private PriorityService $priorities) {}

    public function index(PaginatedListRequest $request): JsonResponse
    {
        $this->authorize('viewAny', TaskPriority::class);

        return TaskPriorityResource::collection($this->priorities->paginate($request->perPage()))->response();
    }

    public function store(TaskPriorityRequest $request): JsonResponse
    {
        $this->authorize('create', TaskPriority::class);

        $priority = $this->priorities->create($request->validated());

        return TaskPriorityResource::make($priority)
            ->additional(['message' => "Gravidade \"{$priority->name}\" criada."])
            ->response()
            ->setStatusCode(201);
    }

    public function update(TaskPriorityRequest $request, TaskPriority $priority): JsonResponse
    {
        $this->authorize('update', $priority);

        $priority = $this->priorities->update($priority, $request->validated());

        return TaskPriorityResource::make($priority)
            ->additional(['message' => "Gravidade \"{$priority->name}\" atualizada."])
            ->response();
    }

    public function destroy(TaskPriority $priority): JsonResponse
    {
        $this->authorize('delete', $priority);

        $this->priorities->delete($priority);

        return response()->json(['message' => "Gravidade \"{$priority->name}\" excluída."]);
    }
}
