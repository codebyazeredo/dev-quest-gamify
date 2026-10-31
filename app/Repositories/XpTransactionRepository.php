<?php

namespace App\Repositories;

use App\Enums\XpSourceType;
use App\Models\Task;
use App\Models\User;
use App\Models\XpTransaction;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class XpTransactionRepository extends Repository
{
    protected function model(): string
    {
        return XpTransaction::class;
    }

    public function findTaskCompletionBonus(Task $task): ?XpTransaction
    {
        return $this->query()
            ->where('source_type', XpSourceType::TASK)
            ->where('source_id', $task->id)
            ->first();
    }

    public function totalFor(User $user): int
    {
        return (int) $this->query()->where('user_id', $user->id)->sum('amount');
    }

    public function sumForUserSince(User $user, CarbonInterface $since): int
    {
        return (int) $this->query()->where('user_id', $user->id)->where('created_at', '>=', $since)->sum('amount');
    }

    public function countUsersWithTotalAbove(int $total): int
    {
        return $this->query()
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('SUM(amount) > ?', [$total])
            ->get()
            ->count();
    }

    public function latestFor(User $user, int $limit): Collection
    {
        return $this->query()->where('user_id', $user->id)->latest()->limit($limit)->get();
    }

    public function paginateLatestFor(User $user, int $perPage): LengthAwarePaginator
    {
        return $this->query()->where('user_id', $user->id)->latest()->paginate($perPage);
    }

    /** @param  array{user_id: ?int, source: ?int, from: ?string, to: ?string}  $filters */
    public function paginateAudit(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->auditQuery($filters)->with('user')->latest()->latest('id')->paginate($perPage);
    }

    /**
     * @param  array{user_id: ?int, source: ?int, from: ?string, to: ?string}  $filters
     * @return LazyCollection<int, XpTransaction>
     */
    public function lazyAudit(array $filters): LazyCollection
    {
        return $this->auditQuery($filters)->with('user')->latest()->latest('id')->lazy();
    }

    /** @param  array{user_id: ?int, source: ?int, from: ?string, to: ?string}  $filters */
    public function sumAudit(array $filters): int
    {
        return (int) $this->auditQuery($filters)->sum('amount');
    }

    /** @param  array{user_id: ?int, source: ?int, from: ?string, to: ?string}  $filters */
    private function auditQuery(array $filters): Builder
    {
        return $this->query()
            ->when($filters['user_id'], fn (Builder $query, int $userId) => $query->where('user_id', $userId))
            ->when($filters['source'], fn (Builder $query, int $source) => $query->where('source_type', $source))
            ->when($filters['from'], fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'], fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to));
    }

    public function sumSince(CarbonInterface $since): int
    {
        return (int) $this->query()->where('created_at', '>=', $since)->sum('amount');
    }

    public function countActiveUsersSince(CarbonInterface $since): int
    {
        return $this->query()->where('created_at', '>=', $since)->distinct()->count('user_id');
    }

    /** @return Collection<string, int> soma de XP por dia (Y-m-d => total) */
    public function sumPerDaySince(CarbonInterface $since): Collection
    {
        return $this->query()
            ->where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->map(fn ($total) => (int) $total);
    }

    /** @return Collection<int, object{user_id: int, name: string, total: int}> */
    public function topUsersSince(?CarbonInterface $since, int $limit): Collection
    {
        return $this->query()
            ->join('users', 'users.id', '=', 'xp_transactions.user_id')
            ->when($since !== null, fn ($query) => $query->where('xp_transactions.created_at', '>=', $since))
            ->selectRaw('users.id as user_id, users.name as name, SUM(xp_transactions.amount) as total')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }
}
