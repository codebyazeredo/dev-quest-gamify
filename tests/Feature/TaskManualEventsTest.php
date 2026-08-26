<?php

namespace Tests\Feature;

use App\Enums\TaskEventType;
use App\Enums\TaskStatus;
use App\Livewire\Task\Kanban;
use App\Livewire\Task\Show;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskEvent;
use App\Models\User;
use App\Services\TaskService;
use Database\Seeders\LevelSeeder;
use Database\Seeders\TaskEventRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskManualEventsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelSeeder::class);
        $this->seed(TaskEventRuleSeeder::class);
    }

    private function approvedBoardWithTask(?User $assignee = null): array
    {
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $approved = $board->columns->firstWhere('status', TaskStatus::APPROVED);

        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $approved->id,
            'status' => TaskStatus::APPROVED,
            'assigned_to' => $assignee?->id,
        ]);

        return [$board, $task];
    }

    public function test_assigned_developer_can_mark_homologation_and_deploy(): void
    {
        $developer = User::factory()->developer()->create();
        [, $task] = $this->approvedBoardWithTask($developer);

        Livewire::actingAs($developer)
            ->test(Show::class, ['task' => $task])
            ->call('markHomologationCompleted')
            ->call('markDeployed');

        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::HOMOLOGATION_COMPLETED->value]);
        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::DEPLOYED->value]);
    }

    public function test_admin_and_product_owner_can_mark_homologation_and_deploy(): void
    {
        $admin = User::factory()->admin()->create();
        $po = User::factory()->productOwner()->create();
        [, $task] = $this->approvedBoardWithTask();

        Livewire::actingAs($admin)->test(Show::class, ['task' => $task])->call('markHomologationCompleted');
        Livewire::actingAs($po)->test(Show::class, ['task' => $task])->call('markDeployed');

        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::HOMOLOGATION_COMPLETED->value]);
        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::DEPLOYED->value]);
    }

    public function test_unrelated_developer_is_forbidden(): void
    {
        $assignee = User::factory()->developer()->create();
        $other = User::factory()->developer()->create();
        [, $task] = $this->approvedBoardWithTask($assignee);

        Livewire::actingAs($other)
            ->test(Show::class, ['task' => $task])
            ->call('markHomologationCompleted')
            ->assertForbidden();
    }

    public function test_repeat_calls_are_idempotent(): void
    {
        $developer = User::factory()->developer()->create();
        [, $task] = $this->approvedBoardWithTask($developer);

        $component = Livewire::actingAs($developer)->test(Show::class, ['task' => $task]);
        $component->call('markHomologationCompleted');
        $component->call('markDeployed');
        $component->call('markDeployed');

        $this->assertSame(
            1,
            TaskEvent::where('task_id', $task->id)->where('type', TaskEventType::DEPLOYED->value)->count()
        );
    }

    public function test_marking_homologation_and_deployed_is_forbidden_outside_aprovado(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $testing = $board->columns->firstWhere('status', TaskStatus::TESTING);
        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $testing->id,
            'status' => TaskStatus::TESTING,
            'assigned_to' => $developer->id,
        ]);

        Livewire::actingAs($developer)
            ->test(Show::class, ['task' => $task])
            ->call('markHomologationCompleted')
            ->assertForbidden();
    }

    public function test_marking_homologation_moves_the_task_straight_to_done(): void
    {
        $developer = User::factory()->developer()->create();
        [$board, $task] = $this->approvedBoardWithTask($developer);
        $done = $board->columns->firstWhere('status', TaskStatus::DONE);

        Livewire::actingAs($developer)
            ->test(Show::class, ['task' => $task])
            ->call('markHomologationCompleted');

        $task->refresh();
        $this->assertSame($done->id, $task->column_id);
        $this->assertSame(TaskStatus::DONE, $task->status);
        $this->assertNotNull($task->completed_at);
        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::COMPLETED->value]);
    }

    public function test_deployed_can_only_be_marked_once_the_task_is_done(): void
    {
        $developer = User::factory()->developer()->create();
        [, $task] = $this->approvedBoardWithTask($developer);

        Livewire::actingAs($developer)
            ->test(Show::class, ['task' => $task])
            ->call('markDeployed')
            ->assertForbidden();
    }

    public function test_kanban_shows_pending_badges_until_marked(): void
    {
        $developer = User::factory()->developer()->create();
        [$board, $task] = $this->approvedBoardWithTask($developer);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->assertSee('Não homologado');

        app(TaskService::class)->markHomologationCompleted($task, $developer);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->assertSee('Não implantado')
            ->assertDontSee('Não homologado');

        app(TaskService::class)->markDeployed($task, $developer);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->assertDontSee('Não implantado');
    }

    public function test_nobody_can_drag_a_task_directly_into_done(): void
    {
        $admin = User::factory()->admin()->create();
        [$board, $task] = $this->approvedBoardWithTask();
        $done = $board->columns->firstWhere('status', TaskStatus::DONE);

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $done->id, 0)
            ->assertDispatched('toast', fn ($name, $params) => $params['toast']['type'] === 'error');

        $this->assertSame(TaskStatus::APPROVED, $task->refresh()->status);
    }
}
