<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskEventType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventRuleRequest;
use App\Http\Resources\EventRuleResource;
use App\Models\TaskEventRule;
use App\Services\Admin\EventRuleService;
use Illuminate\Http\JsonResponse;

class EventRuleController extends Controller
{
    public function __construct(private EventRuleService $rules) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', TaskEventRule::class);

        return EventRuleResource::collection($this->rules->overview())->response();
    }

    public function store(EventRuleRequest $request): JsonResponse
    {
        $this->authorize('create', TaskEventRule::class);

        $validated = $request->validated();
        $rule = $this->rules->create(TaskEventType::from($validated['type']), $validated['xp_reward'], $validated['active']);

        return EventRuleResource::make(['type' => $rule->type, 'rule' => $rule])
            ->additional(['message' => "Regra \"{$rule->type->label()}\" criada."])
            ->response()
            ->setStatusCode(201);
    }

    public function update(EventRuleRequest $request, int $type): JsonResponse
    {
        $rule = $this->rules->findOrFail($type);

        $this->authorize('update', $rule);

        $validated = $request->validated();
        $rule = $this->rules->update($rule, $validated['xp_reward'], $validated['active']);

        return EventRuleResource::make(['type' => $rule->type, 'rule' => $rule])
            ->additional(['message' => "Regra \"{$rule->type->label()}\" atualizada."])
            ->response();
    }
}
