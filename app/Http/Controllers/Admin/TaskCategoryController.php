<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TaskCategoryRequest;
use App\Http\Requests\PaginatedListRequest;
use App\Http\Resources\TaskCategoryResource;
use App\Models\TaskCategory;
use App\Services\Admin\CategoryService;
use Illuminate\Http\JsonResponse;

class TaskCategoryController extends Controller
{
    public function __construct(private CategoryService $categories) {}

    public function index(PaginatedListRequest $request): JsonResponse
    {
        $this->authorize('viewAny', TaskCategory::class);

        return TaskCategoryResource::collection($this->categories->paginate($request->perPage()))->response();
    }

    public function store(TaskCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', TaskCategory::class);

        $category = $this->categories->create($request->validated());

        return TaskCategoryResource::make($category)
            ->additional(['message' => "Categoria \"{$category->name}\" criada."])
            ->response()
            ->setStatusCode(201);
    }

    public function update(TaskCategoryRequest $request, TaskCategory $category): JsonResponse
    {
        $this->authorize('update', $category);

        $category = $this->categories->update($category, $request->validated());

        return TaskCategoryResource::make($category)
            ->additional(['message' => "Categoria \"{$category->name}\" atualizada."])
            ->response();
    }

    public function destroy(TaskCategory $category): JsonResponse
    {
        $this->authorize('delete', $category);

        $this->categories->delete($category);

        return response()->json(['message' => "Categoria \"{$category->name}\" excluída."]);
    }
}
