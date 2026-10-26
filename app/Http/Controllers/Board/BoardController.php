<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Http\Requests\Board\BoardRequest;
use App\Http\Resources\BoardResource;
use App\Models\Board;
use App\Services\BoardService;
use Illuminate\Http\JsonResponse;

class BoardController extends Controller
{
    public function __construct(private BoardService $boards) {}

    public function store(BoardRequest $request): JsonResponse
    {
        $this->authorize('create', Board::class);

        $board = $this->boards->create($request->safe()->except('seed_default_columns'), $request->withDefaultColumns());

        return BoardResource::make($board)
            ->additional(['message' => "Quadro \"{$board->name}\" criado."])
            ->response()
            ->setStatusCode(201);
    }

    public function update(BoardRequest $request, Board $board): JsonResponse
    {
        $this->authorize('update', $board);

        $board = $this->boards->update($board, $request->validated());

        return BoardResource::make($board)
            ->additional(['message' => 'As alterações do quadro foram salvas.'])
            ->response();
    }
}
