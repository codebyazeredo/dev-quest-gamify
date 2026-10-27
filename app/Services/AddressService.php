<?php

namespace App\Services;

use App\Models\Address;
use App\Repositories\AddressRepository;
use App\Repositories\CountryRepository;

class AddressService
{
    public function __construct(
        private AddressRepository $addresses,
        private CountryRepository $countries,
    ) {}

    public function resolve(array $data): Address
    {
        return $this->addresses->firstOrCreateShared(
            [
                'country_id' => $data['country_id'] ?? $this->countries->defaultId(),
                'cep' => $this->normalizePostalCode($data['cep']),
                'logradouro' => trim($data['logradouro']),
                'numero' => $this->optional($data, 'numero'),
                'complemento' => $this->optional($data, 'complemento'),
                'bairro' => $this->optional($data, 'bairro'),
                'cidade' => trim($data['cidade']),
                'estado' => strtoupper($data['estado']),
                'referencia' => $this->optional($data, 'referencia'),
            ],
            ['latitude' => $data['latitude'] ?? null, 'longitude' => $data['longitude'] ?? null],
        );
    }

    public function releaseIfUnused(?int $addressId): void
    {
        if ($addressId !== null) {
            $this->addresses->deleteIfUnused($addressId);
        }
    }

    /** @return array{countries: array<int, array{id: int, name: string}>, default_country_id: int} */
    public function countryOptions(): array
    {
        return [
            'countries' => $this->countries->all()->map(fn ($country) => ['id' => $country->id, 'name' => $country->name])->all(),
            'default_country_id' => $this->countries->defaultId(),
        ];
    }

    private function normalizePostalCode(string $postalCode): string
    {
        $digits = preg_replace('/\D/', '', $postalCode);

        return strlen($digits) === 8 ? substr($digits, 0, 5).'-'.substr($digits, 5) : trim($postalCode);
    }

    private function optional(array $data, string $key): ?string
    {
        return filled($data[$key] ?? null) ? trim($data[$key]) : null;
    }
}
