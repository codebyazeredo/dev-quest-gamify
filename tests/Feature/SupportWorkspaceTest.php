<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Middleware\EnsurePortalClient;
use App\Mail\TicketReplied;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Models\User;
use App\Support\StoredFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupportWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function support(): User
    {
        return User::factory()->suporte()->create();
    }

    public function test_views_filter_the_queue_and_summary_counts_match(): void
    {
        $me = $this->support();
        $other = $this->support();
        Ticket::factory()->create(['title' => 'Meu', 'assigned_to' => $me->id]);
        Ticket::factory()->create(['title' => 'Do outro', 'assigned_to' => $other->id]);
        Ticket::factory()->create(['title' => 'Livre']);
        Ticket::factory()->create(['title' => 'Vencido', 'due_at' => now()->subHour()]);
        Ticket::factory()->create(['title' => 'Esperando', 'status' => TicketStatus::WAITING_CUSTOMER]);
        Ticket::factory()->create(['title' => 'Fechado', 'status' => TicketStatus::RESOLVED, 'due_at' => now()->subDay()]);

        $this->actingAs($me)->getJson(route('api.tickets.index', ['view' => 'mine']))
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Meu');

        $this->actingAs($me)->getJson(route('api.tickets.index', ['view' => 'unassigned']))
            ->assertJsonCount(3, 'data');

        $this->actingAs($me)->getJson(route('api.tickets.index', ['view' => 'overdue']))
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Vencido');

        $this->actingAs($me)->getJson(route('api.tickets.index', ['view' => 'waiting']))
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Esperando');

        $this->actingAs($me)->getJson(route('api.tickets.index'))
            ->assertJsonPath('summary.open', 5)
            ->assertJsonPath('summary.mine', 1)
            ->assertJsonPath('summary.unassigned', 3)
            ->assertJsonPath('summary.overdue', 1)
            ->assertJsonPath('summary.waiting', 1)
            ->assertJsonPath('summary.resolved', 1)
            ->assertJsonPath('summary.all', 6);
    }

    public function test_queue_is_sorted_by_deadline_and_filters_by_priority_assignee_and_search(): void
    {
        $me = $this->support();
        Ticket::factory()->create(['title' => 'Depois', 'due_at' => now()->addDays(2), 'priority' => TicketPriority::LOW]);
        Ticket::factory()->create(['title' => 'Antes', 'due_at' => now()->addHour(), 'priority' => TicketPriority::URGENT, 'assigned_to' => $me->id]);

        $this->actingAs($me)->getJson(route('api.tickets.index'))
            ->assertJsonPath('data.0.title', 'Antes')->assertJsonPath('data.1.title', 'Depois');

        $this->actingAs($me)->getJson(route('api.tickets.index', ['priority' => 'low']))
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Depois');

        $this->actingAs($me)->getJson(route('api.tickets.index', ['assignee' => 'me']))
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Antes')->assertJsonPath('data.0.assignee.id', $me->id);

        $this->actingAs($me)->getJson(route('api.tickets.index', ['search' => 'Depo']))
            ->assertJsonCount(1, 'data');

        $this->actingAs($me)->getJson(route('api.tickets.index', ['priority' => 'invalida']))->assertUnprocessable();
    }

    public function test_staff_claims_and_assigns_tickets_but_only_to_support_staff(): void
    {
        $me = $this->support();
        $teammate = $this->support();
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($me)->postJson(route('api.tickets.claim', $ticket))->assertOk();
        $this->assertSame($me->id, $ticket->fresh()->assigned_to);

        $this->actingAs($me)->putJson(route('api.tickets.assignee', $ticket), ['user_id' => $teammate->id])->assertOk();
        $this->assertSame($teammate->id, $ticket->fresh()->assigned_to);

        $this->actingAs($me)->putJson(route('api.tickets.assignee', $ticket), ['user_id' => $developer->id])
            ->assertUnprocessable()->assertJsonValidationErrors('user_id');

        $this->actingAs($me)->putJson(route('api.tickets.assignee', $ticket), ['user_id' => null])->assertOk();
        $this->assertNull($ticket->fresh()->assigned_to);
    }

    public function test_first_public_reply_registers_response_time_and_assigns_the_replier(): void
    {
        Mail::fake();
        $me = $this->support();
        $ticket = Ticket::factory()->create();

        $this->actingAs($me)->postJson(route('api.tickets.reply', $ticket), ['body' => 'Olá, já estamos vendo.'])->assertCreated();

        $ticket->refresh();
        $this->assertNotNull($ticket->first_responded_at);
        $this->assertSame($me->id, $ticket->assigned_to);
    }

    public function test_internal_note_never_emails_the_client_or_changes_the_ticket(): void
    {
        Mail::fake();
        $ticket = Ticket::factory()->create();

        $this->actingAs($this->support())
            ->postJson(route('api.tickets.reply', $ticket), ['body' => 'Cliente já teve esse erro antes.', 'is_internal' => true])
            ->assertCreated()
            ->assertJsonPath('message', 'Nota interna adicionada. O cliente não a verá.');

        Mail::assertNothingQueued();
        Mail::assertNothingSent();

        $ticket->refresh();
        $this->assertSame(TicketStatus::OPEN, $ticket->status);
        $this->assertNull($ticket->first_responded_at);
        $this->assertNull($ticket->assigned_to);
        $this->assertTrue(TicketMessage::firstOrFail()->is_internal);
    }

    public function test_portal_never_shows_internal_notes_or_their_attachments(): void
    {
        Storage::fake(StoredFile::DISK);
        $client = Client::factory()->create();
        $ticket = Ticket::factory()->for($client)->create();
        $public = TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => $this->support()->id, 'body' => 'Resposta pública visível']);
        $internal = TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => $this->support()->id, 'body' => 'Segredo interno da equipe', 'is_internal' => true]);
        $attachment = TicketAttachment::factory()->for($ticket)->create(['ticket_message_id' => $internal->id]);
        Storage::disk(StoredFile::DISK)->put($attachment->path, 'conteudo');

        $session = [EnsurePortalClient::SESSION_KEY => $client->id];

        $this->withSession($session)->get(route('portal.tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Resposta pública visível')
            ->assertDontSee('Segredo interno da equipe');

        $this->withSession($session)->get(route('portal.attachments.show', $attachment))->assertNotFound();
        $this->assertNotNull($public);
    }

    public function test_internal_attachments_are_hidden_from_non_support_users_with_task_access(): void
    {
        Storage::fake(StoredFile::DISK);
        $ticket = Ticket::factory()->create();
        $internal = TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => $this->support()->id, 'body' => 'Nota', 'is_internal' => true]);
        $attachment = TicketAttachment::factory()->for($ticket)->create(['ticket_message_id' => $internal->id]);
        Storage::disk(StoredFile::DISK)->put($attachment->path, 'conteudo');

        $this->actingAs($this->support())->get(route('ticket-attachments.show', $attachment))->assertOk();
        $this->actingAs(User::factory()->developer()->create())->get(route('ticket-attachments.show', $attachment))->assertForbidden();
    }

    public function test_staff_summary_reports_average_first_response_time(): void
    {
        $me = $this->support();
        Ticket::factory()->create(['created_at' => now()->subHours(2), 'first_responded_at' => now()->subHour()]);
        Ticket::factory()->create(['created_at' => now()->subHours(4), 'first_responded_at' => now()->subHours(2)]);

        $this->actingAs($me)->getJson(route('api.tickets.index'))
            ->assertJsonPath('summary.avg_first_response_minutes', 90);
    }

    public function test_ticket_page_and_queue_page_render_for_support(): void
    {
        $me = $this->support();
        $ticket = Ticket::factory()->create(['assigned_to' => $me->id]);
        Ticket::factory()->create(['client_id' => $ticket->client_id, 'title' => 'Histórico do cliente']);
        TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => $me->id, 'body' => 'Nota visível à equipe', 'is_internal' => true]);

        $this->actingAs($me)->get(route('tickets.index'))->assertOk()->assertSee('Fila de atendimento');
        $this->actingAs($me)->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Nota interna')
            ->assertSee('Nota visível à equipe')
            ->assertSee('Histórico do cliente');
    }

    public function test_customer_reply_still_sends_no_email_and_staff_public_reply_does(): void
    {
        Mail::fake();
        $ticket = Ticket::factory()->create();

        $this->actingAs($this->support())->postJson(route('api.tickets.reply', $ticket), ['body' => 'Resposta ao cliente'])->assertCreated();

        Mail::assertQueued(TicketReplied::class, 1);
    }
}
