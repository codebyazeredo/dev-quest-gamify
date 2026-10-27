<?php

namespace App\Services\Admin;

use App\Exceptions\DeletionBlockedException;
use App\Models\Client;
use App\Repositories\ClientRepository;
use App\Services\AddressService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ClientService
{
    public function __construct(
        private ClientRepository $clients,
        private AddressService $addresses,
        private BusinessSectorService $sectors,
    ) {}

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->clients->paginate($perPage);
    }

    /** @return array<string, mixed> */
    public function formOptions(): array
    {
        return [
            ...$this->addresses->countryOptions(),
            'sectors' => $this->sectors->all()->map(fn ($sector) => ['id' => $sector->id, 'name' => $sector->name])->all(),
        ];
    }

    public function create(array $data): Client
    {
        return $this->clients->create($this->mapAttributes($data));
    }

    public function update(Client $client, array $data): Client
    {
        return DB::transaction(function () use ($client, $data) {
            $previousAddressId = $client->address_id;

            $client = $this->clients->update($client, $this->mapAttributes($data));

            $this->addresses->releaseIfUnused($previousAddressId === $client->address_id ? null : $previousAddressId);

            return $client;
        });
    }

    public function delete(Client $client): void
    {
        if ($this->clients->hasTickets($client)) {
            throw new DeletionBlockedException('Este cliente possui chamados. Desative-o em vez de excluir.');
        }

        $this->clients->delete($client);
        $this->addresses->releaseIfUnused($client->address_id);
    }

    private function mapAttributes(array $data): array
    {
        return [
            'razao_social' => $data['razao_social'],
            'cnpj' => preg_replace('/\D/', '', $data['cnpj']),
            'email' => mb_strtolower($data['email']),
            'business_sector_id' => $data['business_sector_id'] ?? null,
            'address_id' => filled($data['cep'] ?? null) ? $this->addresses->resolve($data)->id : null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];
    }
}
