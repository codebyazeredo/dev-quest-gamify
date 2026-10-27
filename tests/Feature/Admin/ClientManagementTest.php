<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_CNPJ = '11.222.333/0001-81';

    public function test_admin_can_create_update_and_delete_a_client_storing_only_cnpj_digits(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.clients.store'), [
                'razao_social' => 'Acme Ltda',
                'cnpj' => self::VALID_CNPJ,
                'email' => 'Contato@Acme.com',
            ])
            ->assertCreated();

        $client = Client::where('razao_social', 'Acme Ltda')->firstOrFail();

        $this->assertSame('11222333000181', $client->cnpj);
        $this->assertSame('contato@acme.com', $client->email);
        $this->assertTrue($client->is_active);

        $this->actingAs($admin)
            ->putJson(route('api.clients.update', $client), [
                'razao_social' => 'Acme S.A.',
                'cnpj' => '11222333000181',
                'email' => 'ti@acme.com',
                'is_active' => false,
            ])
            ->assertOk();

        $this->assertFalse($client->refresh()->is_active);

        $this->actingAs($admin)->deleteJson(route('api.clients.destroy', $client))->assertOk();

        $this->assertNull(Client::find($client->id));
    }

    public function test_invalid_and_duplicated_cnpj_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        Client::factory()->create(['cnpj' => '11222333000181']);

        $this->actingAs($admin)
            ->postJson(route('api.clients.store'), ['razao_social' => 'Outra', 'cnpj' => '11.222.333/0001-82', 'email' => 'a@b.com'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cnpj');

        $this->actingAs($admin)
            ->postJson(route('api.clients.store'), ['razao_social' => 'Outra', 'cnpj' => self::VALID_CNPJ, 'email' => 'a@b.com'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cnpj');
    }

    public function test_deleting_a_client_with_tickets_is_blocked(): void
    {
        $admin = User::factory()->admin()->create();
        $client = Client::factory()->create();
        Ticket::factory()->for($client)->create();

        $this->actingAs($admin)->deleteJson(route('api.clients.destroy', $client))
            ->assertStatus(409)
            ->assertJsonPath('message', 'Este cliente possui chamados. Desative-o em vez de excluir.');

        $this->assertNotNull(Client::find($client->id));
    }

    public function test_developer_is_forbidden_on_the_page_and_on_the_api(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/admin/clients')->assertForbidden();
        $this->actingAs($developer)->getJson(route('api.clients.index'))->assertForbidden();
    }
}
