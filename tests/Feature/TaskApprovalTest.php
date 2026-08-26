<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Enums\XpSourceType;
use App\Livewire\Task\Kanban;
use App\Livewire\Task\Show;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use App\Models\XpTransaction;
use Database\Seeders\LevelSeeder;
use Database\Seeders\TaskEventRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

        Livewire::actingAs($tester)
            ->test(Show::class, ['task' => $task])
            ->call('approve');

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

        Livewire::actingAs($user)
            ->test(Show::class, ['task' => $task])
            ->call('approve')
            ->assertForbidden();

        $this->assertSame(TaskStatus::TESTING, $task->refresh()->status);
    }

    public function test_reject_requires_a_reason(): void
    {
        $tester = User::factory()->tester()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $task = $this->taskInTesting($board, User::factory()->developer()->create());

        Livewire::actingAs($tester)
            ->test(Show::class, ['task' => $task])
            ->set('rejectionReasonInput', '')
            ->call('reject')
            ->assertHasErrors(['rejectionReasonInput']);

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

        Livewire::actingAs($tester)
            ->test(Show::class, ['task' => $task])
            ->set('rejectionReasonInput', 'Botão de salvar não funciona')
            ->call('reject');

        $task->refresh();
        $this->assertSame($todo->id, $task->column_id);
        $this->assertSame('Botão de salvar não funciona', $task->rejection_reason);
        $this->assertNotNull($task->rejected_at);

        Livewire::actingAs($assignee)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $doing->id, 0);

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

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $done->id, 0)
            ->assertDispatched('toast', fn ($name, $params) => $params['toast']['type'] === 'error');

        $this->assertSame(TaskStatus::TESTING, $task->refresh()->status);
    }

    public function test_tester_xp_is_granted_only_when_the_task_actually_completes(): void
    {
        $tester = User::factory()->tester()->create();
        $assignee = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $task = $this->taskInTesting($board, $assignee);
        $done = $board->columns->firstWhere('status', TaskStatus::DONE);

        Livewire::actingAs($tester)
            ->test(Show::class, ['task' => $task])
            ->call('approve');

        $this->assertSame(0, XpTransaction::where('user_id', $tester->id)->where('source_type', XpSourceType::TASK_EVENT)->count());

        Livewire::actingAs($assignee)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $done->id, 0);

        $this->assertGreaterThan(0, XpTransaction::where('user_id', $tester->id)->where('source_type', XpSourceType::TASK_EVENT)->count());
    }
}
