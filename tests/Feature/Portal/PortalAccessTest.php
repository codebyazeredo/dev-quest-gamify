<?php

namespace Tests\Feature\Portal;

use App\Http\Middleware\EnsurePortalClient;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_signs_in_with_cnpj_and_registered_email(): void
    {
        $client = Client::factory()->create(['cnpj' => '11222333000181', 'email' => 'contato@acme.com']);

        $this->post(route('portal.login.store'), ['cnpj' => '11.222.333/0001-81', 'email' => 'CONTATO@acme.com'])
            ->assertRedirect(route('portal.tickets.index'));

        $this->assertSame($client->id, session(EnsurePortalClient::SESSION_KEY));
    }

    public function test_wrong_email_inactive_client_and_unknown_cnpj_are_rejected(): void
    {
        Client::factory()->create(['cnpj' => '11222333000181', 'email' => 'contato@acme.com']);
        Client::factory()->inactive()->create(['cnpj' => '45723174000110', 'email' => 'off@acme.com']);

        $this->post(route('portal.login.store'), ['cnpj' => '11222333000181', 'email' => 'outro@acme.com'])
            ->assertSessionHasErrors('cnpj');

        $this->post(route('portal.login.store'), ['cnpj' => '45723174000110', 'email' => 'off@acme.com'])
            ->assertSessionHasErrors('cnpj');

        $this->post(route('portal.login.store'), ['cnpj' => '11222333000182', 'email' => 'contato@acme.com'])
            ->assertSessionHasErrors('cnpj');

        $this->assertNull(session(EnsurePortalClient::SESSION_KEY));
    }

    public function test_guest_is_redirected_to_the_portal_login(): void
    {
        $this->get(route('portal.tickets.index'))->assertRedirect(route('portal.login'));
    }

    public function test_deactivated_client_loses_access_on_the_next_request(): void
    {
        $client = Client::factory()->inactive()->create();

        $this->withSession([EnsurePortalClient::SESSION_KEY => $client->id])
            ->get(route('portal.tickets.index'))
            ->assertRedirect(route('portal.login'));
    }

    public function test_client_can_sign_out(): void
    {
        $client = Client::factory()->create();

        $this->withSession([EnsurePortalClient::SESSION_KEY => $client->id])
            ->post(route('portal.logout'))
            ->assertRedirect(route('portal.login'));

        $this->assertNull(session(EnsurePortalClient::SESSION_KEY));
    }
}
