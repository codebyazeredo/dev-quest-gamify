<?php

namespace App\Repositories;

use App\Models\XpTransaction;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class XpTransactionRepository extends Repository
{
    protected function model(): string
    {
        return XpTransaction::class;
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
