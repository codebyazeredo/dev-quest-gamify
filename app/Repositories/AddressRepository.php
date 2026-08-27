<?php

namespace App\Repositories;

use App\Models\Address;

class AddressRepository extends Repository
{
    protected function model(): string
    {
        return Address::class;
    }

    public function updateOrCreateForPerson(int $personId, array $attributes): Address
    {
        return Address::updateOrCreate(['person_id' => $personId], $attributes);
    }
}
