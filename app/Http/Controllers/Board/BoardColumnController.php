<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Http\Requests\Board\ColumnMilestoneRequest;
use App\Http\Requests\Board\ColumnNameRequest;
use App\Http\Resources\BoardColumnResource;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Services\BoardColumnService;
use Illuminate\Http\JsonResponse;

class BoardColumnController extends Controller
{
    public function __construct(private BoardColumnService $columns) {}

    public function store(ColumnNameRequest $request, Board $board): JsonResponse
    {
        $this->authorize('update', $board);

        $column = $this->columns->create($board, $request->validated('name'));

        return BoardColumnResource::make($column)
            ->additional(['message' => "Coluna \"{$column->name}\" adicionada."])
            ->response()
            ->setStatusCode(201);
    }

    public function rename(ColumnNameRequest $request, BoardColumn $column): JsonResponse
    {
        $this->authorize('update', $column->board);

        $column = $this->columns->rename($column, $request->validated('name'));

        return BoardColumnResource::make($column)->response();
    }

    public function setMilestone(ColumnMilestoneRequest $request, BoardColumn $column): JsonResponse
    {
        $this->authorize('update', $column->board);

        $column = $this->columns->setMilestone($column, $request->milestone());

        return BoardColumnResource::make($column)
            ->additional(['message' => 'Marco de pontuação da coluna atualizado.'])
            ->response();
    }

    public function moveUp(BoardColumn $column): JsonResponse
    {
        $this->authorize('update', $column->board);

        $this->columns->moveUp($column);

        return response()->json(['message' => "Coluna \"{$column->name}\" movida para a esquerda."]);
    }

    public function moveDown(BoardColumn $column): JsonResponse
    {
        $this->authorize('update', $column->board);

        $this->columns->moveDown($column);

        return response()->json(['message' => "Coluna \"{$column->name}\" movida para a direita."]);
    }

    public function destroy(BoardColumn $column): JsonResponse
    {
        $this->authorize('update', $column->board);

        $this->columns->delete($column);

        return response()->json(['message' => "Coluna \"{$column->name}\" excluída do quadro."]);
    }
}
