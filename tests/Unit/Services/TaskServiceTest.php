<?php

namespace Tests\Unit\Services;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_freezes_base_points_and_multiplier_from_category_and_priority(): void
    {
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $category = TaskCategory::factory()->create(['base_points' => 10]);
        $creator = User::factory()->create();

        $task = app(TaskService::class)->create([
            'title' => 'Fix login bug',
            'category_id' => $category->id,
            'column_id' => $column->id,
            'priority' => TaskPriority::CRITICAL->value,
        ], $creator);

        $this->assertSame(10, $task->base_points);
        $this->assertSame('5.00', (string) $task->priority_multiplier);
        $this->assertEquals(TaskStatus::TODO, $task->status);
    }

    public function test_xp_value_multiplies_base_points_by_priority_multiplier(): void
    {
        $task = Task::factory()->create([
            'base_points' => 10,
            'priority' => TaskPriority::CRITICAL,
            'priority_multiplier' => TaskPriority::CRITICAL->multiplier(),
        ]);

        $this->assertSame(50, $task->xpValue());
    }

    public function test_move_updates_column_and_synced_status(): void
    {
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $destination = BoardColumn::factory()->for($board)->status(TaskStatus::DOING)->create();
        $task = $this->createTask($board, $origin);

        app(TaskService::class)->move($task, $destination, 0, User::factory()->create());

        $task->refresh();
        $this->assertSame($destination->id, $task->column_id);
        $this->assertEquals(TaskStatus::DOING, $task->status);
    }

    public function test_move_sets_completed_at_when_entering_final_column(): void
    {
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::TESTING)->create();
        $done = BoardColumn::factory()->for($board)->status(TaskStatus::DONE)->create(['is_final' => true]);
        $task = $this->createTask($board, $origin);

        app(TaskService::class)->move($task, $done, 0, User::factory()->create());

        $this->assertNotNull($task->refresh()->completed_at);
    }

    public function test_move_clears_completed_at_when_leaving_final_column(): void
    {
        $board = Board::factory()->create();
        $done = BoardColumn::factory()->for($board)->status(TaskStatus::DONE)->create(['is_final' => true]);
        $testing = BoardColumn::factory()->for($board)->status(TaskStatus::TESTING)->create();
        $task = $this->createTask($board, $done, ['completed_at' => now()]);

        app(TaskService::class)->move($task, $testing, 0, User::factory()->create());

        $this->assertNull($task->refresh()->completed_at);
    }

    public function test_move_sets_started_at_on_first_transition_out_of_backlog(): void
    {
        $board = Board::factory()->create();
        $backlog = BoardColumn::factory()->for($board)->status(TaskStatus::BACKLOG)->create();
        $todo = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $task = $this->createTask($board, $backlog, ['started_at' => null]);

        app(TaskService::class)->move($task, $todo, 0, User::factory()->create());

        $this->assertNotNull($task->refresh()->started_at);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function createTask(Board $board, BoardColumn $column, array $overrides = []): Task
    {
        return Task::factory()->create(array_merge([
            'board_id' => $board->id,
            'column_id' => $column->id,
            'status' => $column->status,
            'position' => 0,
        ], $overrides));
    }
}
