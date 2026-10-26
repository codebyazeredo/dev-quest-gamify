<?php

namespace App\Repositories;

use App\Enums\TitleUnlockType;
use App\Models\Title;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TitleRepository extends Repository
{
    protected function model(): string
    {
        return Title::class;
    }

    protected function query(): Builder
    {
        return parent::query()->with(['achievement', 'challenge'])->orderBy('name');
    }

    public function hasUnlocks(Title $title): bool
    {
        return $title->userTitles()->exists();
    }

    public function paginateFiltered(int $perPage, string $search = '', string $unlockType = '', string $status = ''): LengthAwarePaginator
    {
        return $this->query()
            ->withCount('userTitles')
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->when($unlockType !== '', fn (Builder $query) => $query->where('unlock_type', $unlockType))
            ->when($status !== '', fn (Builder $query) => $query->where('active', $status === 'active'))
            ->paginate($perPage);
    }

    public function activeByUnlockType(TitleUnlockType $type): Collection
    {
        return $this->query()->where('active', true)->where('unlock_type', $type)->get();
    }

    public function activeForChallenge(int $challengeId): Collection
    {
        return $this->query()
            ->where('active', true)
            ->where('unlock_type', TitleUnlockType::CHALLENGE)
            ->where('challenge_id', $challengeId)
            ->get();
    }

    public function activeLockedFor(array $unlockedTitleIds): Collection
    {
        return $this->query()->where('active', true)->whereNotIn('id', $unlockedTitleIds)->get();
    }
}
