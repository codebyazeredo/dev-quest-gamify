<?php

namespace Tests\Feature;

use App\Models\BusinessSector;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use App\Rules\ValidCnpj;
use Database\Seeders\BusinessSectorSeeder;
use Database\Seeders\ClientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_clients_with_valid_cnpj_address_and_sector(): void
    {
        $this->seed([BusinessSectorSeeder::class, ClientSeeder::class]);

        $this->assertSame(6, Client::count());
        $this->assertGreaterThanOrEqual(10, BusinessSector::count());

        foreach (Client::with('address', 'businessSector')->get() as $client) {
            $this->assertTrue(ValidCnpj::isValid($client->cnpj));
            $this->assertNotNull($client->address);
            $this->assertNotNull($client->businessSector);
        }
    }

    public function test_seeds_tickets_with_staff_replies_and_is_idempotent(): void
    {
        $support = User::factory()->suporte()->create();

        $this->seed([BusinessSectorSeeder::class, ClientSeeder::class]);
        $this->seed(ClientSeeder::class);

        $this->assertSame(6, Client::count());
        $this->assertSame(6, Ticket::count());
        $this->assertTrue(Ticket::query()->whereHas('messages', fn ($query) => $query->where('user_id', $support->id))->exists());
        $this->assertTrue(Ticket::query()->whereHas('messages', fn ($query) => $query->whereNull('user_id'))->exists());
    }
}
