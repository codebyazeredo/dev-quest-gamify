<?php

namespace App\Services\Admin;

use App\Exceptions\DeletionBlockedException;
use App\Models\Client;
use App\Repositories\ClientRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClientService
{
    public function __construct(private ClientRepository $clients) {}

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->clients->paginate($perPage);
    }

    public function create(array $data): Client
    {
        return $this->clients->create($this->mapAttributes($data));
    }

    public function update(Client $client, array $data): Client
    {
        return $this->clients->update($client, $this->mapAttributes($data));
    }

    public function delete(Client $client): void
    {
        if ($this->clients->hasTickets($client)) {
            throw new DeletionBlockedException('Este cliente possui chamados. Desative-o em vez de excluir.');
        }

        $this->clients->delete($client);
    }

    private function mapAttributes(array $data): array
    {
        return [
            'razao_social' => $data['razao_social'],
            'cnpj' => preg_replace('/\D/', '', $data['cnpj']),
            'email' => mb_strtolower($data['email']),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];
    }
}
