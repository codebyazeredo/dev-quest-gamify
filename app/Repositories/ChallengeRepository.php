<?php

namespace App\Repositories;

use App\Enums\ChallengeType;
use App\Models\Challenge;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ChallengeRepository extends Repository
{
    protected function model(): string
    {
        return Challenge::class;
    }

    protected function query(): Builder
    {
        return parent::query()->orderByDesc('starts_at');
    }

    /** Desafios ativos de um tipo cujo período contém o instante informado. */
    public function activeOfTypeAt(ChallengeType $type, CarbonInterface $moment): Collection
    {
        return $this->query()
            ->where('active', true)
            ->where('type', $type)
            ->where('starts_at', '<=', $moment)
            ->where('ends_at', '>=', $moment)
            ->get();
    }

    public function hasProgress(Challenge $challenge): bool
    {
        return $challenge->userChallenges()->exists();
    }

    public function paginateFiltered(int $perPage, string $search = '', string $status = ''): LengthAwarePaginator
    {
        return $this->query()
            ->with('titles')
            ->withCount([
                'userChallenges as participants_count',
                'userChallenges as completions_count' => fn (Builder $query) => $query->whereNotNull('completed_at'),
            ])
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->when($status === 'inactive', fn (Builder $query) => $query->where('active', false))
            ->when($status === 'running', fn (Builder $query) => $query->where('active', true)->where('starts_at', '<=', now())->where('ends_at', '>=', now()))
            ->when($status === 'scheduled', fn (Builder $query) => $query->where('active', true)->where('starts_at', '>', now()))
            ->when($status === 'ended', fn (Builder $query) => $query->where('active', true)->where('ends_at', '<', now()))
            ->paginate($perPage);
    }

    public function nameExists(string $name): bool
    {
        return $this->query()->where('name', $name)->exists();
    }

    public function activeCurrentAndUpcoming(): Collection
    {
        return $this->query()
            ->with('titles')
            ->where('active', true)
            ->where('ends_at', '>=', now())
            ->reorder('ends_at')
            ->get();
    }
}
