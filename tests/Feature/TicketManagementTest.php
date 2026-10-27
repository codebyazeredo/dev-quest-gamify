<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Board;
use App\Models\BusinessSector;
use App\Models\BoardColumn;
use App\Models\Client;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskPriority;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use App\Support\StoredFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_staff_lists_and_filters_tickets(): void
    {
        $support = User::factory()->suporte()->create();
        Ticket::factory()->create(['title' => 'Aberto A']);
        Ticket::factory()->create(['title' => 'Resolvido B', 'status' => TicketStatus::RESOLVED]);

        $this->actingAs($support)->getJson(route('api.tickets.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->actingAs($support)->getJson(route('api.tickets.index', ['status' => 'resolved']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Resolvido B');
    }

    public function test_staff_reply_is_stored_and_moves_an_open_ticket_to_in_progress(): void
    {
        $support = User::factory()->suporte()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($support)
            ->postJson(route('api.tickets.reply', $ticket), ['body' => 'Estamos analisando.'])
            ->assertCreated();

        $this->assertDatabaseHas('ticket_messages', ['ticket_id' => $ticket->id, 'user_id' => $support->id]);
        $this->assertSame(TicketStatus::IN_PROGRESS, $ticket->refresh()->status);
    }

    public function test_staff_changes_the_ticket_status(): void
    {
        $productOwner = User::factory()->productOwner()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($productOwner)
            ->putJson(route('api.tickets.status', $ticket), ['status' => 'resolved'])
            ->assertOk();

        $this->assertSame(TicketStatus::RESOLVED, $ticket->refresh()->status);

        $this->actingAs($productOwner)
            ->putJson(route('api.tickets.status', $ticket), ['status' => 'inexistente'])
            ->assertUnprocessable();
    }

    public function test_ticket_becomes_a_task_only_once(): void
    {
        $support = User::factory()->suporte()->create();
        $ticket = Ticket::factory()->for(Client::factory()->create(['razao_social' => 'Acme']))->create(['title' => 'Relatório quebrado']);
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->untagged()->create();

        $payload = [
            'board_id' => $board->id,
            'column_id' => $column->id,
            'title' => 'Corrigir relatório de vendas',
            'category_id' => TaskCategory::factory()->create()->id,
            'priority_id' => TaskPriority::factory()->create()->id,
        ];

        $this->actingAs($support)->postJson(route('api.tickets.task', $ticket), $payload)->assertCreated();

        $task = Task::where('title', 'Corrigir relatório de vendas')->firstOrFail();

        $this->assertSame($task->id, $ticket->refresh()->task_id);
        $this->assertSame(TicketStatus::IN_PROGRESS, $ticket->status);
        $this->assertStringContainsString("Chamado #{$ticket->id}", $task->description);

        $this->actingAs($support)->postJson(route('api.tickets.task', $ticket), $payload)
            ->assertStatus(409)
            ->assertJsonPath('message', 'Este chamado já foi convertido em tarefa.');

        $this->assertSame(1, Task::count());
    }

    public function test_developer_cannot_see_or_handle_tickets(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($developer)->get(route('tickets.index'))->assertForbidden();
        $this->actingAs($developer)->get(route('tickets.show', $ticket))->assertForbidden();
        $this->actingAs($developer)->getJson(route('api.tickets.index'))->assertForbidden();
        $this->actingAs($developer)->postJson(route('api.tickets.reply', $ticket), ['body' => 'Oi'])->assertForbidden();
    }

    public function test_ticket_page_renders_for_support_staff(): void
    {
        $support = User::factory()->suporte()->create();
        $ticket = Ticket::factory()->create(['title' => 'Falha no login']);

        $this->actingAs($support)->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Falha no login');
    }

    public function test_task_column_must_belong_to_the_chosen_board_and_title_is_required(): void
    {
        $support = User::factory()->suporte()->create();
        $ticket = Ticket::factory()->create();
        $board = Board::factory()->create();
        $foreignColumn = BoardColumn::factory()->for(Board::factory()->create())->untagged()->create();

        $payload = [
            'board_id' => $board->id,
            'column_id' => $foreignColumn->id,
            'title' => '',
            'category_id' => TaskCategory::factory()->create()->id,
            'priority_id' => TaskPriority::factory()->create()->id,
        ];

        $this->actingAs($support)->postJson(route('api.tickets.task', $ticket), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['column_id', 'title']);

        $this->assertSame(0, Task::count());
    }

    public function test_staff_can_reply_before_converting_and_attach_files(): void
    {
        Storage::fake(StoredFile::DISK);
        $support = User::factory()->suporte()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($support)
            ->post(route('api.tickets.reply', $ticket), [
                'body' => 'Segue o passo a passo.',
                'files' => [UploadedFile::fake()->create('passo-a-passo.pdf', 50, 'application/pdf')],
            ], ['Accept' => 'application/json'])
            ->assertCreated();

        $attachment = TicketAttachment::firstOrFail();

        $this->assertSame($support->id, $attachment->user_id);
        $this->assertNotNull($attachment->ticket_message_id);
        $this->assertNull($ticket->refresh()->task_id);

        $this->actingAs($support)->get(route('ticket-attachments.show', $attachment))->assertOk();
        $this->actingAs(User::factory()->developer()->create())->get(route('ticket-attachments.show', $attachment))->assertForbidden();
    }

    public function test_ticket_page_shows_client_details_and_task_progress(): void
    {
        $support = User::factory()->suporte()->create();
        $sector = BusinessSector::factory()->create(['name' => 'Saúde']);
        $client = Client::factory()->create(['business_sector_id' => $sector->id]);
        $ticket = Ticket::factory()->for($client)->create();

        $board = Board::factory()->create();
        $first = BoardColumn::factory()->for($board)->untagged()->create(['name' => 'Backlog', 'position' => 0]);
        BoardColumn::factory()->for($board)->untagged()->create(['name' => 'Em andamento', 'position' => 1]);
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $first->id]);
        $ticket->update(['task_id' => $task->id]);

        $this->actingAs($support)->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Saúde')
            ->assertSee($client->formatted_cnpj)
            ->assertSee('50%');
    }
}
