<?php

namespace Tests\Feature;

use App\Models\BusinessSector;
use App\Models\Client;
use App\Rules\ValidCnpj;
use Database\Seeders\BusinessSectorSeeder;
use Database\Seeders\ClientSeeder;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_clients_with_valid_cnpj_address_and_sector(): void
    {
        $this->seed([CountrySeeder::class, BusinessSectorSeeder::class, ClientSeeder::class]);

        $this->assertGreaterThanOrEqual(20, Client::count());
        $this->assertGreaterThanOrEqual(10, BusinessSector::count());

        foreach (Client::with('address', 'businessSector')->get() as $client) {
            $this->assertTrue(ValidCnpj::isValid($client->cnpj), "CNPJ inválido: {$client->cnpj}");
            $this->assertNotNull($client->address);
            $this->assertNotNull($client->businessSector);
        }
    }

    public function test_seeding_clients_twice_does_not_duplicate_them(): void
    {
        $this->seed([CountrySeeder::class, BusinessSectorSeeder::class, ClientSeeder::class]);
        $total = Client::count();

        $this->seed(ClientSeeder::class);

        $this->assertSame($total, Client::count());
    }
}
