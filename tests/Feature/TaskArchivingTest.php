<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskArchivingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_archive_and_unarchive_a_task(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->status(TaskStatus::DONE)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id, 'status' => TaskStatus::DONE]);

        $this->actingAs($admin)->postJson(route('api.tasks.archive', $task))->assertOk();

        $this->assertNotNull($task->refresh()->archived_at);

        $this->actingAs($admin)->postJson(route('api.tasks.unarchive', $task))->assertOk();

        $this->assertNull($task->refresh()->archived_at);
    }

    public function test_product_owner_can_archive_a_task(): void
    {
        $po = User::factory()->productOwner()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->untagged()->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id, 'status' => null]);

        $this->actingAs($po)->postJson(route('api.tasks.archive', $task))->assertOk();

        $this->assertNotNull($task->refresh()->archived_at);
    }

    public function test_developer_cannot_archive_a_task(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->untagged()->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id, 'status' => null, 'assigned_to' => $developer->id]);

        $this->actingAs($developer)->postJson(route('api.tasks.archive', $task))->assertForbidden();

        $this->assertNull($task->refresh()->archived_at);
    }

    public function test_archived_tasks_do_not_appear_on_the_kanban_board(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->untagged()->create();

        $archived = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $column->id,
            'status' => null,
            'archived_at' => now(),
        ]);
        $active = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $column->id,
            'status' => null,
        ]);

        $this->actingAs($admin)->get(route('boards.show', $board))
            ->assertOk()
            ->assertViewHas('board', function (Board $kanban) use ($archived, $active) {
                $tasks = $kanban->columns->first()->tasks;

                return ! $tasks->contains('id', $archived->id) && $tasks->contains('id', $active->id);
            });
    }

    public function test_archive_page_splits_completed_and_not_completed_tasks(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->untagged()->create();

        $completed = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $column->id,
            'status' => null,
            'archived_at' => now(),
            'completed_at' => now(),
        ]);
        $notCompleted = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $column->id,
            'status' => null,
            'archived_at' => now(),
            'completed_at' => null,
        ]);
        $notArchived = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $column->id,
            'status' => null,
        ]);

        $this->actingAs($admin)->get(route('boards.archive', $board))
            ->assertOk()
            ->assertViewHas('completed', fn ($tasks) => $tasks->contains('id', $completed->id) && $tasks->count() === 1)
            ->assertViewHas('notCompleted', fn ($tasks) => $tasks->contains('id', $notCompleted->id) && $tasks->count() === 1)
            ->assertDontSee($notArchived->title);
    }
}
