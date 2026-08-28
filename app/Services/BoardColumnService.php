<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Exceptions\DeletionBlockedException;
use App\Exceptions\DuplicateEntryException;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Repositories\BoardColumnRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BoardColumnService
{
    public function __construct(private BoardColumnRepository $columns) {}

    public function create(Board $board, string $name): BoardColumn
    {
        $column = $board->columns()->create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(8),
            'position' => $board->columns()->count(),
            'is_final' => false,
            'status' => null,
        ]);

        $column->update(['slug' => Str::slug($name).'-'.$column->id]);

        return $column;
    }

    public function rename(BoardColumn $column, string $name): BoardColumn
    {
        $column->update(['name' => $name]);

        return $column;
    }

    public function setMilestone(BoardColumn $column, ?TaskStatus $status): BoardColumn
    {
        if ($status !== null) {
            $conflict = BoardColumn::where('board_id', $column->board_id)
                ->where('status', $status)
                ->where('id', '!=', $column->id)
                ->exists();

            if ($conflict) {
                throw new DuplicateEntryException('Este quadro já tem uma coluna marcada com este marco.');
            }
        }

        $column->update([
            'status' => $status,
            'is_final' => $status === TaskStatus::DONE,
        ]);

        return $column;
    }

    public function moveUp(BoardColumn $column): void
    {
        $this->swapPosition($column, -1);
    }

    public function moveDown(BoardColumn $column): void
    {
        $this->swapPosition($column, 1);
    }

    public function delete(BoardColumn $column): void
    {
        if ($this->columns->hasTasks($column)) {
            throw new DeletionBlockedException('Esta coluna ainda possui tarefas.');
        }

        $boardId = $column->board_id;

        $column->delete();

        BoardColumn::where('board_id', $boardId)->orderBy('position')->get()->values()
            ->each(fn (BoardColumn $remaining, int $index) => $remaining->update(['position' => $index]));
    }

    private function swapPosition(BoardColumn $column, int $direction): void
    {
        DB::transaction(function () use ($column, $direction) {
            $columns = BoardColumn::where('board_id', $column->board_id)->orderBy('position')->get();
            $index = $columns->search(fn (BoardColumn $candidate) => $candidate->id === $column->id);
            $targetIndex = $index + $direction;

            if ($index === false || $targetIndex < 0 || $targetIndex >= $columns->count()) {
                return;
            }

            $current = $columns[$index];
            $target = $columns[$targetIndex];

            [$currentPosition, $targetPosition] = [$current->position, $target->position];

            $current->update(['position' => $targetPosition]);
            $target->update(['position' => $currentPosition]);
        });
    }
}
