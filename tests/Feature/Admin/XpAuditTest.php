<?php

namespace Tests\Feature\Admin;

use App\Enums\XpSourceType;
use App\Models\Task;
use App\Models\TaskEvent;
use App\Models\User;
use App\Models\XpTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class XpAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_accesses_the_audit(): void
    {
        $this->actingAs(User::factory()->developer()->create())->get(route('admin.xp-audit'))->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get(route('admin.xp-audit'))->assertOk();
    }

    public function test_lists_who_received_how_much_why_and_links_the_task(): void
    {
        $developer = User::factory()->developer()->create(['name' => 'Maria Dev']);
        $task = Task::factory()->create();
        $event = TaskEvent::factory()->create(['task_id' => $task->id, 'user_id' => $developer->id]);
        XpTransaction::factory()->create([
            'user_id' => $developer->id,
            'amount' => 20,
            'source_type' => XpSourceType::TASK_EVENT,
            'source_id' => $event->id,
            'description' => 'Implantação - Tarefa #'.$task->id,
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.xp-audit'))
            ->assertOk()
            ->assertSee('Maria Dev')
            ->assertSee('Evento de tarefa')
            ->assertSee('Implantação - Tarefa #'.$task->id)
            ->assertSee(route('tasks.show', $task));
    }

    public function test_filters_by_user_source_and_period_and_sums_the_balance(): void
    {
        $maria = User::factory()->developer()->create();
        $joao = User::factory()->developer()->create();
        XpTransaction::factory()->create(['user_id' => $maria->id, 'amount' => 10, 'source_type' => XpSourceType::CHECKIN, 'description' => 'Do Maria checkin']);
        XpTransaction::factory()->create(['user_id' => $maria->id, 'amount' => 50, 'source_type' => XpSourceType::BONUS, 'description' => 'Do Maria bonus']);
        XpTransaction::factory()->create(['user_id' => $joao->id, 'amount' => 7, 'source_type' => XpSourceType::CHECKIN, 'description' => 'Do Joao checkin']);
        XpTransaction::factory()->create(['user_id' => $maria->id, 'amount' => 99, 'source_type' => XpSourceType::CHECKIN, 'description' => 'Do Maria antigo', 'created_at' => now()->subMonths(2)]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.xp-audit', [
                'user_id' => $maria->id,
                'source' => XpSourceType::CHECKIN->value,
                'from' => now()->subDay()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Do Maria checkin')
            ->assertDontSee('Do Maria bonus')
            ->assertDontSee('Do Joao checkin')
            ->assertDontSee('Do Maria antigo')
            ->assertSee('10 XP');
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.xp-audit', ['source' => 99, 'from' => '2026-02-10', 'to' => '2026-02-01']))
            ->assertSessionHasErrors(['source', 'to']);
    }
}
