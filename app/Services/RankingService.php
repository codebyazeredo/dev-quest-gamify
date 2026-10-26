<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RankingService
{
    public function __construct(private UserRepository $users) {}

    public function paginate(string $role, string $period, int $perPage): LengthAwarePaginator
    {
        return $this->users->paginateRankedByRole($role, $this->periodStart($period), $perPage);
    }

    private function periodStart(string $period): ?CarbonInterface
    {
        return match ($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => null,
        };
    }
}
