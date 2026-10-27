<?php

namespace App\Repositories;

use App\Models\Address;

class AddressRepository extends Repository
{
    protected function model(): string
    {
        return Address::class;
    }

    /** Reaproveita o endereço que tenha exatamente estes dados; `$extra` só vale na criação. */
    public function firstOrCreateShared(array $identity, array $extra = []): Address
    {
        return Address::firstOrCreate($identity, $extra);
    }

    /** Ao incluir um novo dono de endereço (ex.: clientes), acrescente a relação dele aqui. */
    public function deleteIfUnused(int $addressId): void
    {
        Address::query()->whereKey($addressId)->whereDoesntHave('people')->delete();
    }
}
