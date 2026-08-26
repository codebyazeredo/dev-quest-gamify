<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Livewire\Task\Kanban;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $destination->id, 0);

        $this->assertSame($destination->id, $task->refresh()->column_id);
    }

    public function test_developer_can_move_own_assigned_task(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $destination = BoardColumn::factory()->for($board)->status(TaskStatus::DOING)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $origin->id, 'status' => TaskStatus::TODO, 'assigned_to' => $developer->id]);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $destination->id, 0);

        $this->assertSame($destination->id, $task->refresh()->column_id);
    }

    public function test_developer_cannot_move_an_unassigned_task(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $destination = BoardColumn::factory()->for($board)->status(TaskStatus::DOING)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $origin->id, 'status' => TaskStatus::TODO, 'assigned_to' => null]);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $destination->id, 0)
            ->assertDispatched('toast', fn ($name, $params) => $params['toast']['type'] === 'error');

        $this->assertSame($origin->id, $task->refresh()->column_id);
    }

    public function test_moving_a_task_syncs_status_to_destination_column(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $destination = BoardColumn::factory()->for($board)->status(TaskStatus::REVIEW)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $origin->id, 'status' => TaskStatus::TODO]);

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $destination->id, 0);

        $this->assertEquals(TaskStatus::REVIEW, $task->refresh()->status);
    }
}
