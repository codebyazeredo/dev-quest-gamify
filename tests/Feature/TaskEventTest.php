<?php

namespace Tests\Feature;

use App\Enums\TaskEventType;
use App\Enums\TaskStatus;
use App\Enums\XpSourceType;
use App\Events\TaskCompleted;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskEvent;
use App\Models\User;
use App\Services\TaskService;
use Database\Seeders\LevelSeeder;
use Database\Seeders\TaskEventRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TaskEventTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelSeeder::class);
        $this->seed(TaskEventRuleSeeder::class);
    }

    public function test_moving_into_doing_records_started_with_zero_xp(): void
    {
        $board = $this->boardWithStandardColumns();
        $developer = User::factory()->developer()->create();
        $task = $this->taskIn($board, TaskStatus::TODO, $developer);

        app(TaskService::class)->move($task, $this->columnFor($board, TaskStatus::DOING), 0, $developer);

        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::STARTED->value]);
        $this->assertSame(0, $developer->xpTransactions()->sum('amount'));
    }

    public function test_moving_into_review_records_development_completed_and_grants_xp(): void
    {
        $board = $this->boardWithStandardColumns();
        $developer = User::factory()->developer()->create();
        $task = $this->taskIn($board, TaskStatus::DOING, $developer);

        app(TaskService::class)->move($task, $this->columnFor($board, TaskStatus::REVIEW), 0, $developer);

        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::DEVELOPMENT_COMPLETED->value]);
        $this->assertSame(10, $developer->xpTransactions()->sum('amount'));
    }

    public function test_anti_farming_reentry_does_not_grant_xp_twice(): void
    {
        $board = $this->boardWithStandardColumns();
        $developer = User::factory()->developer()->create();
        $doing = $this->columnFor($board, TaskStatus::DOING);
        $review = $this->columnFor($board, TaskStatus::REVIEW);
        $task = $this->taskIn($board, TaskStatus::DOING, $developer);

        $service = app(TaskService::class);
        $service->move($task, $review, 0, $developer);
        $task->refresh();
        $service->move($task, $doing, 0, $developer);
        $task->refresh();
        $service->move($task, $review, 0, $developer);
        $task->refresh();

        $this->assertSame(
            1,
            TaskEvent::where('task_id', $task->id)->where('type', TaskEventType::DEVELOPMENT_COMPLETED->value)->count()
        );
        $this->assertSame(10, $developer->xpTransactions()->sum('amount'));
    }

    public function test_moving_directly_from_todo_to_done_grants_all_crossed_milestones_and_completion_bonus(): void
    {
        $board = $this->boardWithStandardColumns();
        $developer = User::factory()->developer()->create();
        $category = TaskCategory::factory()->create(['base_points' => 10]);
        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $this->columnFor($board, TaskStatus::TODO)->id,
            'status' => TaskStatus::TODO,
            'assigned_to' => $developer->id,
            'category_id' => $category->id,
            'priority_multiplier' => '1.50',
            'base_points' => 10,
        ]);

        app(TaskService::class)->move($task, $this->columnFor($board, TaskStatus::DONE), 0, $developer);

        $this->assertSame(10 + 10 + 5 + 15, $developer->xpTransactions()->sum('amount'));
    }

    public function test_reaching_final_column_dispatches_task_completed_and_creates_task_sourced_transaction(): void
    {
        Event::fake([TaskCompleted::class]);

        $board = $this->boardWithStandardColumns();
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $this->columnFor($board, TaskStatus::TESTING)->id,
            'status' => TaskStatus::TESTING,
            'assigned_to' => $developer->id,
            'base_points' => 10,
            'priority_multiplier' => '1.00',
        ]);

        app(TaskService::class)->move($task, $this->columnFor($board, TaskStatus::DONE), 0, $developer);

        Event::assertDispatched(TaskCompleted::class);
        $this->assertDatabaseHas('xp_transactions', [
            'source_type' => XpSourceType::TASK->value,
            'source_id' => $task->id,
            'amount' => $task->xpValue(),
        ]);
    }

    public function test_unassigned_task_completion_grants_no_completion_bonus_but_still_rewards_the_creator(): void
    {
        $board = $this->boardWithStandardColumns();
        $admin = User::factory()->admin()->create();
        $creator = User::factory()->create();
        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $this->columnFor($board, TaskStatus::TODO)->id,
            'status' => TaskStatus::TODO,
            'assigned_to' => null,
            'created_by' => $creator->id,
        ]);

        app(TaskService::class)->move($task, $this->columnFor($board, TaskStatus::DONE), 0, $admin);

        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::COMPLETED->value]);

        $this->assertDatabaseCount('xp_transactions', 1);
        $this->assertDatabaseHas('xp_transactions', [
            'user_id' => $creator->id,
            'source_type' => XpSourceType::TASK_EVENT->value,
        ]);
    }

    protected function boardWithStandardColumns(): Board
    {
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);

        return $board->load('columns');
    }

    protected function columnFor(Board $board, TaskStatus $status): BoardColumn
    {
        return $board->columns->firstWhere('status', $status);
    }

    protected function taskIn(Board $board, TaskStatus $status, User $assignee): Task
    {
        return Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $this->columnFor($board, $status)->id,
            'status' => $status,
            'assigned_to' => $assignee->id,
        ]);
    }
}
