<?php

namespace Tests\Feature;

use App\Enums\TaskEventType;
use App\Enums\TaskStatus;
use App\Enums\XpSourceType;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskEventRule;
use App\Models\User;
use App\Models\XpTransaction;
use Database\Seeders\LevelSeeder;
use Database\Seeders\TaskEventRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelSeeder::class);
        $this->seed(TaskEventRuleSeeder::class);
    }

    private function taskInTesting(Board $board, ?User $assignee = null): Task
    {
        $testing = $board->columns->firstWhere('status', TaskStatus::TESTING);

        return Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $testing->id,
            'status' => TaskStatus::TESTING,
            'assigned_to' => $assignee?->id,
        ]);
    }

    public function test_tester_can_approve_a_task_they_are_not_assigned_to(): void
    {
        $tester = User::factory()->tester()->create();
        $assignee = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $task = $this->taskInTesting($board, $assignee);
        $approved = $board->columns->firstWhere('status', TaskStatus::APPROVED);

        $this->actingAs($tester)->postJson(route('api.tasks.approve', $task))->assertOk();

        $task->refresh();

        $this->assertSame($approved->id, $task->column_id);
        $this->assertSame(TaskStatus::APPROVED, $task->status);
        $this->assertSame($tester->id, $task->approved_by);
    }

    public function test_tester_cannot_approve_their_own_assigned_task_even_with_dual_roles(): void
    {
        $user = User::factory()->developer()->create();
        $user->assignRole('tester');
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $task = $this->taskInTesting($board, $user);

        $this->actingAs($user)->postJson(route('api.tasks.approve', $task))->assertForbidden();

        $this->assertSame(TaskStatus::TESTING, $task->refresh()->status);
    }

    public function test_reject_requires_a_reason(): void
    {
        $tester = User::factory()->tester()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $task = $this->taskInTesting($board, User::factory()->developer()->create());

        $this->actingAs($tester)
            ->postJson(route('api.tasks.reject', $task), ['reason' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('reason');

        $this->assertSame(TaskStatus::TESTING, $task->refresh()->status);
    }

    public function test_rejecting_sends_the_task_back_to_todo_with_a_reason_that_clears_once_work_resumes(): void
    {
        $tester = User::factory()->tester()->create();
        $assignee = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $task = $this->taskInTesting($board, $assignee);
        $todo = $board->columns->firstWhere('status', TaskStatus::TODO);
        $doing = $board->columns->firstWhere('status', TaskStatus::DOING);

        $this->actingAs($tester)
            ->postJson(route('api.tasks.reject', $task), ['reason' => 'Botão de salvar não funciona'])
            ->assertOk();

        $task->refresh();

        $this->assertSame($todo->id, $task->column_id);
        $this->assertSame('Botão de salvar não funciona', $task->rejection_reason);
        $this->assertNotNull($task->rejected_at);

        $this->actingAs($assignee)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $doing->id, 'position' => 0])
            ->assertOk();

        $task->refresh();

        $this->assertNull($task->rejection_reason);
        $this->assertNull($task->rejected_at);
    }

    public function test_generic_drag_out_of_testing_is_blocked_for_every_role(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $task = $this->taskInTesting($board);
        $done = $board->columns->firstWhere('status', TaskStatus::DONE);

        $this->actingAs($admin)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $done->id, 'position' => 0])
            ->assertForbidden();

        $this->assertSame(TaskStatus::TESTING, $task->refresh()->status);
    }

    public function test_tester_xp_is_granted_only_when_the_task_actually_completes(): void
    {
        $tester = User::factory()->tester()->create();
        $assignee = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $task = $this->taskInTesting($board, $assignee);

        $this->actingAs($tester)->postJson(route('api.tasks.approve', $task))->assertOk();

        $this->assertSame(0, XpTransaction::where('user_id', $tester->id)->where('source_type', XpSourceType::TASK_EVENT)->count());

        $this->actingAs($assignee)->postJson(route('api.tasks.homologation', $task))->assertOk();
        $this->actingAs($assignee)->postJson(route('api.tasks.deploy', $task))->assertOk();

        $this->assertGreaterThan(0, XpTransaction::where('user_id', $tester->id)->where('source_type', XpSourceType::TASK_EVENT)->count());
    }

    public function test_tester_bonus_scales_as_a_percentage_of_the_tasks_own_xp_value(): void
    {
        TaskEventRule::where('type', TaskEventType::APPROVED)->update(['xp_reward' => 50]);

        $tester = User::factory()->tester()->create();
        $assignee = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $task = $this->taskInTesting($board, $assignee);
        $task->update(['base_points' => 20, 'priority_multiplier' => '2.00']);

        $this->actingAs($tester)->postJson(route('api.tasks.approve', $task))->assertOk();
        $this->actingAs($assignee)->postJson(route('api.tasks.homologation', $task))->assertOk();

        $this->assertSame(20, (int) XpTransaction::where('user_id', $tester->id)->where('source_type', XpSourceType::TASK_EVENT)->sum('amount'));
    }

    public function test_creator_is_rewarded_a_percentage_of_the_tasks_value_once_it_completes(): void
    {
        TaskEventRule::where('type', TaskEventType::CREATION_COMPLETED)->update(['xp_reward' => 25]);

        $creator = User::factory()->suporte()->create();
        $assignee = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $task = $this->taskInTesting($board, $assignee);
        $task->update(['created_by' => $creator->id, 'base_points' => 20, 'priority_multiplier' => '2.00']);

        $this->actingAs(User::factory()->tester()->create())->postJson(route('api.tasks.approve', $task))->assertOk();
        $this->actingAs($assignee)->postJson(route('api.tasks.homologation', $task))->assertOk();

        $this->assertSame(10, (int) XpTransaction::where('user_id', $creator->id)->where('source_type', XpSourceType::TASK_EVENT)->sum('amount'));
    }

    public function test_approving_on_a_board_with_no_approved_column_returns_an_error_instead_of_crashing(): void
    {
        $tester = User::factory()->tester()->create();
        $board = Board::factory()->create();
        $testing = BoardColumn::factory()->for($board)->status(TaskStatus::TESTING)->create();

        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $testing->id,
            'status' => TaskStatus::TESTING,
        ]);

        $this->actingAs($tester)
            ->postJson(route('api.tasks.approve', $task))
            ->assertStatus(409)
            ->assertJsonPath('message', 'Este quadro não tem uma coluna marcada como "Aprovado".');

        $this->assertSame(TaskStatus::TESTING, $task->refresh()->status);
    }

    public function test_late_task_zeroes_the_tester_and_creator_bonuses_too(): void
    {
        $tester = User::factory()->tester()->create();
        $creator = User::factory()->suporte()->create();
        $assignee = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $task = $this->taskInTesting($board, $assignee);
        $task->update(['created_by' => $creator->id, 'due_at' => now()->subDay()]);

        $this->actingAs($tester)->postJson(route('api.tasks.approve', $task))->assertOk();
        $this->actingAs($assignee)->postJson(route('api.tasks.homologation', $task))->assertOk();

        $this->assertTrue($task->refresh()->isLate());
        $this->assertSame(0, XpTransaction::where('user_id', $tester->id)->where('source_type', XpSourceType::TASK_EVENT)->count());
        $this->assertSame(0, XpTransaction::where('user_id', $creator->id)->where('source_type', XpSourceType::TASK_EVENT)->count());
    }
}
