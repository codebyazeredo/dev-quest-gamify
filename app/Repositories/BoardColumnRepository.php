<?php

namespace App\Repositories;

use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;
use Illuminate\Database\Eloquent\Collection;

class BoardColumnRepository extends Repository
{
    protected function model(): string
    {
        return BoardColumn::class;
    }

    public function hasTasks(BoardColumn $column): bool
    {
        return $column->tasks()->exists();
    }

    public function findTaggedWith(Board $board, TaskStatus $status): ?BoardColumn
    {
        return $this->query()
            ->where('board_id', $board->id)
            ->where('status', $status)
            ->first();
    }

    public function findInBoardOrFail(Board $board, int $columnId): BoardColumn
    {
        return $this->query()->where('board_id', $board->id)->findOrFail($columnId);
    }

    public function countFor(Board $board): int
    {
        return $this->query()->where('board_id', $board->id)->count();
    }

    public function createFor(Board $board, array $attributes): BoardColumn
    {
        return $this->create([...$attributes, 'board_id' => $board->id]);
    }

    /** Outra coluna do mesmo quadro já usa este marco de pontuação? */
    public function milestoneTakenByAnother(BoardColumn $column, TaskStatus $status): bool
    {
        return $this->query()
            ->where('board_id', $column->board_id)
            ->where('status', $status)
            ->where('id', '!=', $column->id)
            ->exists();
    }

    /** @return Collection<int, BoardColumn> colunas do quadro na ordem de exibição */
    public function orderedFor(Board $board): Collection
    {
        return $this->query()->where('board_id', $board->id)->orderBy('position')->get();
    }
}
