<?php

namespace App\Repositories;

use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;

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
        return BoardColumn::where('board_id', $board->id)
            ->where('status', $status)
            ->first();
    }
}
