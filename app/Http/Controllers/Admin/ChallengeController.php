<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChallengeRequest;
use App\Http\Requests\PaginatedListRequest;
use App\Http\Resources\ChallengeResource;
use App\Models\Challenge;
use App\Services\Admin\ChallengeService;
use Illuminate\Http\JsonResponse;

class ChallengeController extends Controller
{
    public function __construct(private ChallengeService $challenges) {}

    public function index(PaginatedListRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Challenge::class);

        $challenges = $this->challenges->paginate($request->perPage(), $request->search(), $request->status());

        return ChallengeResource::collection($challenges)->response();
    }

    public function store(ChallengeRequest $request): JsonResponse
    {
        $this->authorize('create', Challenge::class);

        $challenge = $this->challenges->create($request->safe()->except($this->rewardTitleFields()), $request->rewardTitle());

        return ChallengeResource::make($challenge->load('titles'))
            ->additional(['message' => "Desafio \"{$challenge->name}\" criado."])
            ->response()
            ->setStatusCode(201);
    }

    public function update(ChallengeRequest $request, Challenge $challenge): JsonResponse
    {
        $this->authorize('update', $challenge);

        $challenge = $this->challenges->update($challenge, $request->safe()->except($this->rewardTitleFields()), $request->rewardTitle());

        return ChallengeResource::make($challenge->load('titles'))
            ->additional(['message' => "Desafio \"{$challenge->name}\" atualizado."])
            ->response();
    }

    public function toggleActive(Challenge $challenge): JsonResponse
    {
        $this->authorize('update', $challenge);

        $challenge = $this->challenges->toggleActive($challenge);

        return ChallengeResource::make($challenge->load('titles'))
            ->additional(['message' => $challenge->active ? "Desafio \"{$challenge->name}\" ativado." : "Desafio \"{$challenge->name}\" desativado."])
            ->response();
    }

    public function duplicate(Challenge $challenge): JsonResponse
    {
        $this->authorize('create', Challenge::class);

        $copy = $this->challenges->duplicate($challenge);

        return ChallengeResource::make($copy->load('titles'))
            ->additional(['message' => "Desafio \"{$copy->name}\" criado como inativo para o próximo período."])
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Challenge $challenge): JsonResponse
    {
        $this->authorize('delete', $challenge);

        $this->challenges->delete($challenge);

        return response()->json(['message' => "Desafio \"{$challenge->name}\" excluído."]);
    }

    /** @return array<int, string> */
    private function rewardTitleFields(): array
    {
        return ['award_title', 'reward_title_name', 'reward_title_icon'];
    }
}
