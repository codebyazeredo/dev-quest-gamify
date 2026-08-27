<?php

namespace App\Repositories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PersonRepository extends Repository
{
    protected function model(): string
    {
        return Person::class;
    }

    protected function query(): Builder
    {
        return parent::query()->orderBy('nome');
    }

    public function findWithAddressOrFail(int $id): Person
    {
        return $this->query()->with('address')->findOrFail($id);
    }

    public function isLinkedToUser(Person $person): bool
    {
        return $person->user()->exists();
    }

    public function withoutUser(): Collection
    {
        return $this->query()->whereDoesntHave('user')->get();
    }
}
