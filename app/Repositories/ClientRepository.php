<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;

class ClientRepository extends Repository
{
    protected function model(): string
    {
        return Client::class;
    }

    protected function query(): Builder
    {
        return parent::query()->with(['address', 'businessSector'])->orderBy('razao_social');
    }

    public function findActiveByCnpjAndEmail(string $cnpj, string $email): ?Client
    {
        return Client::query()
            ->where('is_active', true)
            ->where('cnpj', $cnpj)
            ->whereRaw('lower(email) = ?', [mb_strtolower($email)])
            ->first();
    }

    public function hasTickets(Client $client): bool
    {
        return $client->tickets()->exists();
    }
}
