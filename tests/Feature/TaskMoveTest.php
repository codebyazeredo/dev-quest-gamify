<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskMoveTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_move_any_task(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $destination = BoardColumn::factory()->for($board)->status(TaskStatus::DOING)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $origin->id, 'status' => TaskStatus::TODO, 'assigned_to' => null]);

        $this->actingAs($admin)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $destination->id, 'position' => 0])
            ->assertOk();

        $this->assertSame($destination->id, $task->refresh()->column_id);
    }

    public function test_developer_can_move_own_assigned_task(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $destination = BoardColumn::factory()->for($board)->status(TaskStatus::DOING)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $origin->id, 'status' => TaskStatus::TODO, 'assigned_to' => $developer->id]);

        $this->actingAs($developer)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $destination->id, 'position' => 0])
            ->assertOk();

        $this->assertSame($destination->id, $task->refresh()->column_id);
    }

    public function test_developer_who_moves_an_unassigned_task_becomes_its_assignee(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $destination = BoardColumn::factory()->for($board)->status(TaskStatus::DOING)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $origin->id, 'status' => TaskStatus::TODO, 'assigned_to' => null]);

        $this->actingAs($developer)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $destination->id, 'position' => 0])
            ->assertOk();

        $task->refresh();

        $this->assertSame($destination->id, $task->column_id);
        $this->assertSame($developer->id, $task->assigned_to);
    }

    public function test_developer_cannot_move_a_task_that_belongs_to_someone_else(): void
    {
        $developer = User::factory()->developer()->create();
        $owner = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $destination = BoardColumn::factory()->for($board)->status(TaskStatus::DOING)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $origin->id, 'status' => TaskStatus::TODO, 'assigned_to' => $owner->id]);

        $response = $this->actingAs($developer)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $destination->id, 'position' => 0])
            ->assertForbidden();

        $this->assertStringContainsString($owner->name, $response->json('message'));

        $task->refresh();

        $this->assertSame($origin->id, $task->column_id);
        $this->assertSame($owner->id, $task->assigned_to);
    }

    public function test_moving_an_unassigned_task_to_testing_does_not_assign_it(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::REVIEW)->create();
        $testing = BoardColumn::factory()->for($board)->status(TaskStatus::TESTING)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $origin->id, 'status' => TaskStatus::REVIEW, 'assigned_to' => null]);

        $this->actingAs($developer)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $testing->id, 'position' => 0])
            ->assertOk();

        $this->assertNull($task->refresh()->assigned_to);
    }

    public function test_moving_a_task_syncs_status_to_destination_column(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $destination = BoardColumn::factory()->for($board)->status(TaskStatus::REVIEW)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $origin->id, 'status' => TaskStatus::TODO]);

        $this->actingAs($admin)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $destination->id, 'position' => 0])
            ->assertOk();

        $this->assertEquals(TaskStatus::REVIEW, $task->refresh()->status);
    }

    public function test_a_column_from_another_board_is_not_found(): void
    {
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->create();
        $foreignColumn = BoardColumn::factory()->for(Board::factory()->create())->create();

        $this->actingAs($admin)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $foreignColumn->id, 'position' => 0])
            ->assertNotFound();
    }
}
