<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TitleRequest;
use App\Http\Requests\PaginatedListRequest;
use App\Http\Resources\TitleResource;
use App\Models\Title;
use App\Services\Admin\TitleService;
use Illuminate\Http\JsonResponse;

class TitleController extends Controller
{
    public function __construct(private TitleService $titles) {}

    public function index(PaginatedListRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Title::class);

        $titles = $this->titles->paginate($request->perPage(), $request->search(), $request->unlockType(), $request->status());

        return TitleResource::collection($titles)
            ->additional(['form_options' => $this->formOptions()])
            ->response();
    }

    public function store(TitleRequest $request): JsonResponse
    {
        $this->authorize('create', Title::class);

        $title = $this->titles->create($request->validated());

        return TitleResource::make($title)
            ->additional(['message' => "Título \"{$title->name}\" criado."])
            ->response()
            ->setStatusCode(201);
    }

    public function update(TitleRequest $request, Title $title): JsonResponse
    {
        $this->authorize('update', $title);

        $title = $this->titles->update($title, $request->validated());

        return TitleResource::make($title)
            ->additional(['message' => "Título \"{$title->name}\" atualizado."])
            ->response();
    }

    public function toggleActive(Title $title): JsonResponse
    {
        $this->authorize('update', $title);

        $title = $this->titles->toggleActive($title);

        return TitleResource::make($title)
            ->additional(['message' => $title->active ? "Título \"{$title->name}\" ativado." : "Título \"{$title->name}\" desativado."])
            ->response();
    }

    public function destroy(Title $title): JsonResponse
    {
        $this->authorize('delete', $title);

        $this->titles->delete($title);

        return response()->json(['message' => "Título \"{$title->name}\" excluído."]);
    }

    /** Conquistas e desafios que podem ser ligados a um título. */
    private function formOptions(): array
    {
        $options = $this->titles->formOptions();

        return [
            'achievements' => $options['achievements']->map(fn ($achievement) => ['id' => $achievement->id, 'label' => $achievement->name])->values(),
            'challenges' => $options['challenges']->map(fn ($challenge) => ['id' => $challenge->id, 'label' => "{$challenge->name} ({$challenge->statusLabel()})"])->values(),
        ];
    }
}
