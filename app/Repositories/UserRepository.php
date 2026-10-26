<?php

namespace App\Repositories;

use App\Models\User;
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
        return parent::query()->with('person')->orderBy('name');
    }

    /** @param  array<int, int>  $ids */
    public function findMany(array $ids): Collection
    {
        return $this->query()->whereKey($ids)->get();
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
