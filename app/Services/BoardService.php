<?php

namespace App\Services;

use App\Models\Board;
use App\Models\User;
use App\Repositories\BoardRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BoardService
{
    public function __construct(
        private BoardRepository $boards,
        private BoardColumnService $columns,
        private UserRepository $users,
    ) {}

    public function listFor(User $user): Collection
    {
        return $this->boards->listWithTaskCount($user->isAdmin());
    }

    public function landingUrl(): string
    {
        $activeBoards = $this->boards->activeBoards();

        return $activeBoards->count() === 1
            ? route('boards.show', $activeBoards->first())
            : route('boards.index');
    }

    public function assignableUsersFor(User $user): Collection
    {
        return $user->isAdmin() || $user->isProductOwner() ? $this->users->allForAssignment() : new Collection;
    }

    public function create(array $data, bool $withDefaultColumns): Board
    {
        return DB::transaction(function () use ($data, $withDefaultColumns) {
            $board = $this->boards->create($data);

            if ($withDefaultColumns) {
                $this->columns->seedDefaults($board);
            }

            return $board;
        });
    }

    public function update(Board $board, array $data): Board
    {
        return $this->boards->update($board, $data);
    }

    public function kanban(Board $board): Board
    {
        return $this->boards->loadKanban($board);
    }

    /** @return array{completed: Collection, notCompleted: Collection} */
    public function archivedTasks(Board $board): array
    {
        $archived = $this->boards->archivedTasks($board);

        return [
            'completed' => $archived->whereNotNull('completed_at')->values(),
            'notCompleted' => $archived->whereNull('completed_at')->values(),
        ];
    }
}
