<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends Repository
{
    protected function model(): string
    {
        return User::class;
    }

    protected function query(): Builder
    {
        return parent::query()->with(['person', 'roles'])->orderBy('name');
    }

    /** @param  array<int, int>  $ids */
    public function findMany(array $ids): Collection
    {
        return $this->query()->whereKey($ids)->get();
    }

    public function paginateRankedByRole(string $role, ?CarbonInterface $since, int $perPage): LengthAwarePaginator
    {
        return $this->query()
            ->reorder()
            ->role($role)
            ->withSum(['xpTransactions as total_xp' => function ($query) use ($since) {
                if ($since !== null) {
                    $query->where('created_at', '>=', $since);
                }
            }], 'amount')
            ->orderByDesc('total_xp')
            ->paginate($perPage);
    }

    public function allForAssignment(): Collection
    {
        return $this->query()->with('selectedTitle')->get();
    }

    public function withRole(string $role): Collection
    {
        return User::role($role)->get();
    }

    public function countWithRole(string $role): int
    {
        return User::role($role)->count();
    }

    public function hasXpTransactions(User $user): bool
    {
        return $user->xpTransactions()->exists();
    }

    public function syncRoles(User $user, array $roles): void
    {
        $user->syncRoles($roles);
    }
}
