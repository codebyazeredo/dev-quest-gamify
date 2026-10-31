<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Board;
use App\Models\Client;
use App\Models\Level;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\XpTransaction;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoScenarioSeederTest extends TestCase
{
    use RefreshDatabase;

    private function totalXpOf(string $email): int
    {
        return (int) XpTransaction::where('user_id', User::where('email', $email)->firstOrFail()->id)->sum('amount');
    }

    private function levelOf(int $xp): int
    {
        return (int) Level::where('xp_required', '<=', $xp)->max('level');
    }

    public function test_seeds_a_tms_company_with_a_realistic_team_boards_clients_and_support_queue(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(2, Board::count());
        $this->assertSame(3, User::role('dev')->count());
        $this->assertGreaterThanOrEqual(20, Client::count());
        $this->assertGreaterThanOrEqual(50, Ticket::count());
        $this->assertGreaterThanOrEqual(40, Task::count());

        foreach ([TicketStatus::OPEN, TicketStatus::IN_PROGRESS, TicketStatus::WAITING_CUSTOMER, TicketStatus::RESOLVED] as $status) {
            $this->assertTrue(Ticket::where('status', $status)->exists(), "Sem chamados com situação {$status->value}");
        }

        $this->assertTrue(TicketMessage::where('is_internal', true)->exists());
        $this->assertTrue(Ticket::whereNotNull('task_id')->exists());
        $this->assertTrue(Ticket::whereNull('first_responded_at')->where('status', TicketStatus::OPEN)->exists());
        $this->assertSame(0, TicketMessage::where('created_at', '>', now())->count());
    }

    public function test_developers_have_distinct_seniority_levels(): void
    {
        $this->seed(DatabaseSeeder::class);

        $senior = $this->levelOf($this->totalXpOf('rafael.monteiro@devquestgamify.test'));
        $mid = $this->levelOf($this->totalXpOf('dev@devquestgamify.test'));
        $junior = $this->levelOf($this->totalXpOf('thiago.barros@devquestgamify.test'));

        $this->assertGreaterThan($mid, $senior);
        $this->assertGreaterThan($junior, $mid);
    }

    public function test_demo_accounts_documented_in_the_readme_still_exist(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (['admin' => 'admin', 'po' => 'product_owner', 'dev' => 'dev', 'tester' => 'tester', 'suporte' => 'suporte'] as $local => $role) {
            $this->assertTrue(User::where('email', "{$local}@devquestgamify.test")->firstOrFail()->hasRole($role));
        }
    }
}
