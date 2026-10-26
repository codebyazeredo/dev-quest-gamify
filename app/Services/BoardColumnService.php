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

    public function findInBoard(Board $board, int $columnId): BoardColumn
    {
        return $this->columns->findInBoardOrFail($board, $columnId);
    }

    /** Uma coluna por marco de pontuação, na ordem do fluxo de desenvolvimento. */
    public function seedDefaults(Board $board): void
    {
        foreach (TaskStatus::cases() as $position => $status) {
            $this->columns->createFor($board, [
                'name' => $status->label(),
                'slug' => Str::slug($status->label()),
                'position' => $position,
                'is_final' => $status === TaskStatus::DONE,
                'status' => $status,
            ]);
        }
    }

    public function create(Board $board, string $name): BoardColumn
    {
        $column = $this->columns->createFor($board, [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(8),
            'position' => $this->columns->countFor($board),
            'is_final' => false,
            'status' => null,
        ]);

        return $this->columns->update($column, ['slug' => Str::slug($name).'-'.$column->id]);
    }

    public function rename(BoardColumn $column, string $name): BoardColumn
    {
        return $this->columns->update($column, ['name' => $name]);
    }

    public function setMilestone(BoardColumn $column, ?TaskStatus $status): BoardColumn
    {
        if ($status !== null && $this->columns->milestoneTakenByAnother($column, $status)) {
            throw new DuplicateEntryException('Este quadro já tem uma coluna marcada com este marco.');
        }

        return $this->columns->update($column, [
            'status' => $status,
            'is_final' => $status === TaskStatus::DONE,
        ]);
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

        $board = $column->board;

        $this->columns->delete($column);

        $this->columns->orderedFor($board)->values()
            ->each(fn (BoardColumn $remaining, int $index) => $this->columns->update($remaining, ['position' => $index]));
    }

    private function swapPosition(BoardColumn $column, int $direction): void
    {
        DB::transaction(function () use ($column, $direction) {
            $columns = $this->columns->orderedFor($column->board);
            $index = $columns->search(fn (BoardColumn $candidate) => $candidate->id === $column->id);
            $targetIndex = $index + $direction;

            if ($index === false || $targetIndex < 0 || $targetIndex >= $columns->count()) {
                return;
            }

            $current = $columns[$index];
            $target = $columns[$targetIndex];

            [$currentPosition, $targetPosition] = [$current->position, $target->position];

            $this->columns->update($current, ['position' => $targetPosition]);
            $this->columns->update($target, ['position' => $currentPosition]);
        });
    }
}
