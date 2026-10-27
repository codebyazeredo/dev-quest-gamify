<?php

namespace Tests\Feature\Admin;

use App\Models\Address;
use App\Models\BusinessSector;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientAddressAndSectorTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'razao_social' => 'Acme Ltda',
            'cnpj' => '11.222.333/0001-81',
            'email' => 'contato@acme.test',
            'cep' => '01310-100',
            'logradouro' => 'Avenida Paulista',
            'numero' => '1000',
            'bairro' => 'Bela Vista',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            ...$overrides,
        ];
    }

    public function test_client_is_created_with_sector_and_address(): void
    {
        $admin = User::factory()->admin()->create();
        $sector = BusinessSector::factory()->create();

        $this->actingAs($admin)
            ->postJson(route('api.clients.store'), $this->payload(['business_sector_id' => $sector->id]))
            ->assertCreated()
            ->assertJsonPath('data.business_sector', $sector->name)
            ->assertJsonPath('data.cidade', 'São Paulo');

        $client = Client::firstOrFail();

        $this->assertSame($sector->id, $client->business_sector_id);
        $this->assertSame('Bela Vista', $client->address->bairro);
        $this->assertSame('Brasil', $client->address->country->name);
    }

    public function test_clients_at_the_same_address_share_one_row(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.clients.store'), $this->payload())->assertCreated();
        $this->actingAs($admin)
            ->postJson(route('api.clients.store'), $this->payload(['razao_social' => 'Beta SA', 'cnpj' => '45723174000110', 'email' => 'beta@test.com']))
            ->assertCreated();

        $this->assertSame(1, Address::count());
        $this->assertSame(2, Address::firstOrFail()->clients()->count());
    }

    public function test_address_is_optional_but_must_be_complete_when_a_cep_is_given(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.clients.store'), ['razao_social' => 'Sem endereço', 'cnpj' => '11222333000181', 'email' => 'a@test.com'])
            ->assertCreated();

        $this->assertNull(Client::firstOrFail()->address_id);

        $this->actingAs($admin)
            ->postJson(route('api.clients.store'), $this->payload(['cnpj' => '45723174000110', 'email' => 'b@test.com', 'cidade' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cidade');
    }

    public function test_clearing_the_address_releases_it_when_unused(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.clients.store'), $this->payload())->assertCreated();

        $client = Client::firstOrFail();

        $this->actingAs($admin)
            ->putJson(route('api.clients.update', $client), ['razao_social' => 'Acme Ltda', 'cnpj' => '11222333000181', 'email' => 'contato@acme.test'])
            ->assertOk();

        $this->assertNull($client->refresh()->address_id);
        $this->assertSame(0, Address::count());
    }

    public function test_index_returns_form_options(): void
    {
        $admin = User::factory()->admin()->create();
        BusinessSector::factory()->create(['name' => 'Varejo']);

        $this->actingAs($admin)->getJson(route('api.clients.index'))
            ->assertOk()
            ->assertJsonPath('sectors.0.name', 'Varejo')
            ->assertJsonPath('countries.0.name', 'Brasil')
            ->assertJsonStructure(['default_country_id']);
    }
}
