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

class TaskBoardVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_developer_only_sees_unassigned_tasks_in_backlog(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $backlog = BoardColumn::factory()->for($board)->status(TaskStatus::BACKLOG)->create();

        $unassigned = Task::factory()->create(['board_id' => $board->id, 'column_id' => $backlog->id, 'status' => TaskStatus::BACKLOG, 'assigned_to' => null]);
        $assignedToSomeoneElse = Task::factory()->create(['board_id' => $board->id, 'column_id' => $backlog->id, 'status' => TaskStatus::BACKLOG, 'assigned_to' => User::factory()->developer()->create()->id]);
        $assignedToSelf = Task::factory()->create(['board_id' => $board->id, 'column_id' => $backlog->id, 'status' => TaskStatus::BACKLOG, 'assigned_to' => $developer->id]);

        $tasks = Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->get('board')->columns->first()->tasks;

        $this->assertTrue($tasks->contains('id', $unassigned->id));
        $this->assertFalse($tasks->contains('id', $assignedToSomeoneElse->id));
        $this->assertFalse($tasks->contains('id', $assignedToSelf->id));
    }

    public function test_developer_only_sees_own_tasks_outside_backlog_and_review(): void
    {
        $developer = User::factory()->developer()->create();
        $otherDeveloper = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $todo = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();

        $mine = Task::factory()->create(['board_id' => $board->id, 'column_id' => $todo->id, 'status' => TaskStatus::TODO, 'assigned_to' => $developer->id]);
        $someoneElses = Task::factory()->create(['board_id' => $board->id, 'column_id' => $todo->id, 'status' => TaskStatus::TODO, 'assigned_to' => $otherDeveloper->id]);

        $tasks = Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->get('board')->columns->first()->tasks;

        $this->assertTrue($tasks->contains('id', $mine->id));
        $this->assertFalse($tasks->contains('id', $someoneElses->id));
    }

    public function test_developer_sees_every_task_in_review_for_peer_sign_off(): void
    {
        $developer = User::factory()->developer()->create();
        $otherDeveloper = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $review = BoardColumn::factory()->for($board)->status(TaskStatus::REVIEW)->create();

        $mine = Task::factory()->create(['board_id' => $board->id, 'column_id' => $review->id, 'status' => TaskStatus::REVIEW, 'assigned_to' => $developer->id]);
        $someoneElses = Task::factory()->create(['board_id' => $board->id, 'column_id' => $review->id, 'status' => TaskStatus::REVIEW, 'assigned_to' => $otherDeveloper->id]);

        $tasks = Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->get('board')->columns->first()->tasks;

        $this->assertTrue($tasks->contains('id', $mine->id));
        $this->assertTrue($tasks->contains('id', $someoneElses->id));
    }

    public function test_developer_sees_unassigned_tasks_in_untagged_columns(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $freeColumn = BoardColumn::factory()->for($board)->untagged()->create();

        $unassigned = Task::factory()->create(['board_id' => $board->id, 'column_id' => $freeColumn->id, 'status' => null, 'assigned_to' => null]);
        $assignedToSomeoneElse = Task::factory()->create(['board_id' => $board->id, 'column_id' => $freeColumn->id, 'status' => null, 'assigned_to' => User::factory()->developer()->create()->id]);
        $assignedToSelf = Task::factory()->create(['board_id' => $board->id, 'column_id' => $freeColumn->id, 'status' => null, 'assigned_to' => $developer->id]);

        $tasks = Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->get('board')->columns->first()->tasks;

        $this->assertTrue($tasks->contains('id', $unassigned->id));
        $this->assertFalse($tasks->contains('id', $assignedToSomeoneElse->id));
        $this->assertTrue($tasks->contains('id', $assignedToSelf->id));
    }

    public function test_admin_sees_every_task_unfiltered(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $backlog = BoardColumn::factory()->for($board)->status(TaskStatus::BACKLOG)->create();

        $unassigned = Task::factory()->create(['board_id' => $board->id, 'column_id' => $backlog->id, 'status' => TaskStatus::BACKLOG, 'assigned_to' => null]);
        $assigned = Task::factory()->create(['board_id' => $board->id, 'column_id' => $backlog->id, 'status' => TaskStatus::BACKLOG, 'assigned_to' => User::factory()->developer()->create()->id]);

        $tasks = Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->get('board')->columns->first()->tasks;

        $this->assertTrue($tasks->contains('id', $unassigned->id));
        $this->assertTrue($tasks->contains('id', $assigned->id));
    }
}
