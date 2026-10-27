<?php

namespace Tests\Feature\Portal;

use App\Http\Middleware\EnsurePortalClient;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Client;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalTicketProgressTest extends TestCase
{
    use RefreshDatabase;

    private function ticketWithTask(array $taskOverrides = [], int $currentColumn = 1): array
    {
        $client = Client::factory()->create();
        $board = Board::factory()->create();
        $columns = collect(['Backlog', 'Em andamento', 'Em revisão', 'Concluído'])
            ->map(fn (string $name, int $position) => BoardColumn::factory()->for($board)->untagged()->create(['name' => $name, 'position' => $position]));

        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $columns[$currentColumn]->id,
            ...$taskOverrides,
        ]);

        return [$client, Ticket::factory()->for($client)->create(['task_id' => $task->id])];
    }

    private function view(Client $client, Ticket $ticket)
    {
        return $this->withSession([EnsurePortalClient::SESSION_KEY => $client->id])->get(route('portal.tickets.show', $ticket));
    }

    public function test_client_sees_the_current_stage_and_percentage_of_the_linked_task(): void
    {
        [$client, $ticket] = $this->ticketWithTask();

        $this->view($client, $ticket)
            ->assertOk()
            ->assertSee('Andamento da solicitação')
            ->assertSee('Em andamento')
            ->assertSee('Backlog')
            ->assertSee('Concluído')
            ->assertSee('50%');
    }

    public function test_assignee_and_due_date_are_shown_without_internal_task_data(): void
    {
        $developer = User::factory()->developer()->create(['name' => 'Marina Souza']);
        [$client, $ticket] = $this->ticketWithTask([
            'assigned_to' => $developer->id,
            'due_at' => now()->addWeek(),
            'title' => 'Título interno da tarefa',
            'branch_name' => 'feature/segredo-interno',
        ]);

        $this->view($client, $ticket)
            ->assertSee('Marina Souza')
            ->assertSee(now()->addWeek()->format('d/m/Y'))
            ->assertDontSee('Título interno da tarefa')
            ->assertDontSee('feature/segredo-interno');
    }

    public function test_completed_task_shows_full_progress(): void
    {
        [$client, $ticket] = $this->ticketWithTask(['completed_at' => now()], 3);

        $this->view($client, $ticket)
            ->assertSee('Concluída')
            ->assertSee('100%');
    }

    public function test_ticket_without_task_has_no_progress_section(): void
    {
        $client = Client::factory()->create();
        $ticket = Ticket::factory()->for($client)->create();

        $this->view($client, $ticket)
            ->assertOk()
            ->assertDontSee('Andamento da solicitação');
    }
}
