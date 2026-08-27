<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

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

    public function hasXpTransactions(User $user): bool
    {
        return $user->xpTransactions()->exists();
    }

    public function syncRoles(User $user, array $roles): void
    {
        $user->syncRoles($roles);
    }
}
