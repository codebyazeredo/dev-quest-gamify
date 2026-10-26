<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AchievementRequest;
use App\Http\Requests\PaginatedListRequest;
use App\Http\Resources\AchievementResource;
use App\Models\Achievement;
use App\Services\Admin\AchievementService;
use Illuminate\Http\JsonResponse;

class AchievementController extends Controller
{
    public function __construct(private AchievementService $achievements) {}

    public function index(PaginatedListRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Achievement::class);

        $achievements = $this->achievements->paginate($request->perPage(), $request->search(), $request->status());

        return AchievementResource::collection($achievements)->response();
    }

    public function store(AchievementRequest $request): JsonResponse
    {
        $this->authorize('create', Achievement::class);

        $achievement = $this->achievements->create($request->safe()->except($this->rewardTitleFields()), $request->rewardTitle());

        return AchievementResource::make($achievement)
            ->additional(['message' => "Conquista \"{$achievement->name}\" criada."])
            ->response()
            ->setStatusCode(201);
    }

    public function update(AchievementRequest $request, Achievement $achievement): JsonResponse
    {
        $this->authorize('update', $achievement);

        $achievement = $this->achievements->update($achievement, $request->safe()->except($this->rewardTitleFields()), $request->rewardTitle());

        return AchievementResource::make($achievement)
            ->additional(['message' => "Conquista \"{$achievement->name}\" atualizada."])
            ->response();
    }

    public function toggleActive(Achievement $achievement): JsonResponse
    {
        $this->authorize('update', $achievement);

        $achievement = $this->achievements->toggleActive($achievement);

        return AchievementResource::make($achievement)
            ->additional(['message' => $achievement->active ? "Conquista \"{$achievement->name}\" ativada." : "Conquista \"{$achievement->name}\" desativada."])
            ->response();
    }

    public function duplicate(Achievement $achievement): JsonResponse
    {
        $this->authorize('create', Achievement::class);

        $copy = $this->achievements->duplicate($achievement);

        return AchievementResource::make($copy)
            ->additional(['message' => "Conquista \"{$copy->name}\" criada como inativa."])
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Achievement $achievement): JsonResponse
    {
        $this->authorize('delete', $achievement);

        $this->achievements->delete($achievement);

        return response()->json(['message' => "Conquista \"{$achievement->name}\" excluída."]);
    }

    /** @return array<int, string> */
    private function rewardTitleFields(): array
    {
        return ['award_title', 'reward_title_name', 'reward_title_icon'];
    }
}
