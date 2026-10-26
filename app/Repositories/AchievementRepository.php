<?php

namespace App\Repositories;

use App\Models\Achievement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AchievementRepository extends Repository
{
    protected function model(): string
    {
        return Achievement::class;
    }

    protected function query(): Builder
    {
        return parent::query()->orderBy('name');
    }

    /** @param  array<int, int>  $excludedIds */
    public function activeExcluding(array $excludedIds): Collection
    {
        return $this->query()->where('active', true)->whereNotIn('id', $excludedIds)->get();
    }

    public function allActive(): Collection
    {
        return $this->query()->where('active', true)->get();
    }

    public function countActive(): int
    {
        return $this->query()->where('active', true)->count();
    }

    public function hasUnlocks(Achievement $achievement): bool
    {
        return $achievement->userAchievements()->exists();
    }

    public function nameExists(string $name): bool
    {
        return $this->query()->where('name', $name)->exists();
    }

    public function paginateFiltered(int $perPage, string $search = '', string $status = ''): LengthAwarePaginator
    {
        return $this->query()
            ->with('title')
            ->withCount('userAchievements')
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->when($status !== '', fn (Builder $query) => $query->where('active', $status === 'active'))
            ->paginate($perPage);
    }
}
