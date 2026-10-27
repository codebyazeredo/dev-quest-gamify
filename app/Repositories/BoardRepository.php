<?php

namespace App\Repositories;

use App\Models\Board;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BoardRepository extends Repository
{
    protected function model(): string
    {
        return Board::class;
    }

    protected function query(): Builder
    {
        return parent::query()->orderBy('name');
    }

    public function listWithTaskCount(bool $includeInactive): Collection
    {
        return $this->query()
            ->when(! $includeInactive, fn (Builder $query) => $query->where('is_active', true))
            ->withCount('tasks')
            ->get();
    }

    public function activeBoards(): Collection
    {
        return $this->query()->where('is_active', true)->get();
    }

    public function findByGithubRepository(string $repositoryName): ?Board
    {
        return $this->query()->whereRaw('LOWER(github_repository) = ?', [strtolower($repositoryName)])->first();
    }

    public function loadKanban(Board $board): Board
    {
        return $board->load([
            'columns.tasks' => fn ($query) => $query->whereNull('archived_at')
                ->withCount('attachments')
                ->with(['category', 'priority', 'assignedTo.person', 'taskEvents']),
        ]);
    }

    public function archivedTasks(Board $board): Collection
    {
        return Task::query()
            ->where('board_id', $board->id)
            ->whereNotNull('archived_at')
            ->withCount('attachments')
            ->with(['category', 'priority', 'assignedTo.person', 'taskEvents'])
            ->orderByDesc('archived_at')
            ->get();
    }
}
