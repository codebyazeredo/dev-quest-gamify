<?php

namespace App\Repositories;

use App\Models\Address;

class AddressRepository extends Repository
{
    protected function model(): string
    {
        return Address::class;
    }

    public function firstOrCreateShared(array $identity, array $extra = []): Address
    {
        return Address::firstOrCreate($identity, $extra);
    }

    public function deleteIfUnused(int $addressId): void
    {
        Address::query()->whereKey($addressId)->whereDoesntHave('people')->whereDoesntHave('clients')->delete();
    }
}
