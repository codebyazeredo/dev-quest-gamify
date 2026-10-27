<?php

namespace Tests\Feature\Portal;

use App\Enums\TicketStatus;
use App\Http\Middleware\EnsurePortalClient;
use App\Models\Client;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalTicketTest extends TestCase
{
    use RefreshDatabase;

    private function signedIn(Client $client): static
    {
        return $this->withSession([EnsurePortalClient::SESSION_KEY => $client->id]);
    }

    public function test_client_opens_a_ticket_and_sees_only_its_own_list(): void
    {
        $client = Client::factory()->create();
        $mine = Ticket::factory()->for($client)->create(['title' => 'Meu chamado antigo']);
        $others = Ticket::factory()->create(['title' => 'Chamado de outra empresa']);

        $this->signedIn($client)
            ->post(route('portal.tickets.store'), ['title' => 'Erro ao emitir nota', 'description' => 'A tela fica em branco ao emitir.'])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', ['client_id' => $client->id, 'title' => 'Erro ao emitir nota', 'status' => TicketStatus::OPEN->value]);

        $this->signedIn($client)->get(route('portal.tickets.index'))
            ->assertOk()
            ->assertSee($mine->title)
            ->assertSee('Erro ao emitir nota')
            ->assertDontSee($others->title);
    }

    public function test_ticket_requires_subject_and_description(): void
    {
        $client = Client::factory()->create();

        $this->signedIn($client)
            ->post(route('portal.tickets.store'), ['title' => 'ab', 'description' => ''])
            ->assertSessionHasErrors(['title', 'description']);
    }

    public function test_client_cannot_open_a_ticket_from_another_company(): void
    {
        $client = Client::factory()->create();
        $foreignTicket = Ticket::factory()->create();

        $this->signedIn($client)->get(route('portal.tickets.show', $foreignTicket))->assertNotFound();
        $this->signedIn($client)
            ->post(route('portal.tickets.reply', $foreignTicket), ['body' => 'Tentando invadir'])
            ->assertNotFound();

        $this->assertDatabaseMissing('ticket_messages', ['ticket_id' => $foreignTicket->id]);
    }

    public function test_replying_to_a_resolved_ticket_reopens_it(): void
    {
        $client = Client::factory()->create();
        $ticket = Ticket::factory()->for($client)->create(['status' => TicketStatus::RESOLVED]);

        $this->signedIn($client)
            ->post(route('portal.tickets.reply', $ticket), ['body' => 'O problema voltou a acontecer.'])
            ->assertRedirect(route('portal.tickets.show', $ticket));

        $this->assertDatabaseHas('ticket_messages', ['ticket_id' => $ticket->id, 'user_id' => null]);
        $this->assertSame(TicketStatus::OPEN, $ticket->refresh()->status);
    }
}
