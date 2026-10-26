<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Models\Title;
use App\Services\TitleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Título exibido no perfil do usuário logado. */
class TitleSelectionController extends Controller
{
    public function __construct(private TitleService $titles) {}

    public function store(Request $request, Title $title): JsonResponse
    {
        $this->authorize('select', $title);

        $this->titles->select($request->user(), $title);

        return response()->json(['message' => "Título \"{$title->name}\" selecionado."]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $this->titles->clear($request->user());

        return response()->json(['message' => 'Título removido do seu perfil.']);
    }
}
