<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Mail\TicketReplied;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TicketSlaTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_ticket_gets_normal_priority_and_sla_deadline(): void
    {
        $ticket = app(TicketService::class)->open(Client::factory()->create(), ['title' => 'Erro', 'description' => 'Detalhes']);

        $this->assertSame(TicketPriority::NORMAL, $ticket->priority);
        $this->assertEqualsWithDelta(now()->addHours(48)->timestamp, $ticket->due_at->timestamp, 5);
    }

    public function test_staff_changes_priority_and_deadline_is_recalculated_from_creation(): void
    {
        $staff = User::factory()->suporte()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($staff)
            ->putJson(route('api.tickets.priority', $ticket), ['priority' => 'urgent'])
            ->assertOk();

        $ticket->refresh();

        $this->assertSame(TicketPriority::URGENT, $ticket->priority);
        $this->assertTrue($ticket->due_at->equalTo($ticket->created_at->copy()->addHours(4)));
    }

    public function test_invalid_priority_is_rejected(): void
    {
        $this->actingAs(User::factory()->suporte()->create())
            ->putJson(route('api.tickets.priority', Ticket::factory()->create()), ['priority' => 'critica'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('priority');
    }

    public function test_overdue_flag_ignores_resolved_tickets(): void
    {
        $this->assertTrue(Ticket::factory()->make(['due_at' => now()->subHour()])->isOverdue());
        $this->assertFalse(Ticket::factory()->make(['due_at' => now()->subHour(), 'status' => TicketStatus::RESOLVED])->isOverdue());
        $this->assertFalse(Ticket::factory()->make(['due_at' => now()->addHour()])->isOverdue());
    }

    public function test_portal_shows_a_client_friendly_label_for_tickets_waiting_on_them(): void
    {
        $this->assertSame('Aguardando sua resposta', TicketStatus::WAITING_CUSTOMER->portalLabel());
        $this->assertSame('Aguardando cliente', TicketStatus::WAITING_CUSTOMER->label());
        $this->assertSame('Aberto', TicketStatus::OPEN->portalLabel());
    }

    public function test_customer_reply_reactivates_ticket_waiting_for_customer(): void
    {
        $ticket = Ticket::factory()->create(['status' => TicketStatus::WAITING_CUSTOMER]);

        app(TicketService::class)->reply($ticket, 'Segue o print.');

        $this->assertSame(TicketStatus::IN_PROGRESS, $ticket->fresh()->status);
    }

    public function test_staff_reply_emails_the_client_and_customer_reply_does_not(): void
    {
        Mail::fake();
        $ticket = Ticket::factory()->create();

        app(TicketService::class)->reply($ticket, 'Pode nos enviar o log?', User::factory()->suporte()->create());
        app(TicketService::class)->reply($ticket, 'Claro, segue.');

        Mail::assertQueued(TicketReplied::class, 1);
        Mail::assertQueued(TicketReplied::class, fn (TicketReplied $mail) => $mail->hasTo($ticket->client->email));
    }
}
