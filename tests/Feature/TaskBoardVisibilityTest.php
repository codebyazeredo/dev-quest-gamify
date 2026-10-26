<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskBoardVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function boardWithTasks(): array
    {
        $board = Board::factory()->create();
        $todo = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $testing = BoardColumn::factory()->for($board)->status(TaskStatus::TESTING)->create();

        $unassigned = Task::factory()->create(['board_id' => $board->id, 'column_id' => $todo->id, 'status' => TaskStatus::TODO, 'assigned_to' => null]);
        $someoneElses = Task::factory()->create(['board_id' => $board->id, 'column_id' => $todo->id, 'status' => TaskStatus::TODO, 'assigned_to' => User::factory()->developer()->create()->id]);
        $inTesting = Task::factory()->create(['board_id' => $board->id, 'column_id' => $testing->id, 'status' => TaskStatus::TESTING, 'assigned_to' => User::factory()->developer()->create()->id]);

        return [$board, collect([$unassigned, $someoneElses, $inTesting])];
    }

    public function test_every_role_sees_every_task_on_the_board(): void
    {
        [$board, $tasks] = $this->boardWithTasks();

        $viewers = [
            User::factory()->admin()->create(),
            User::factory()->developer()->create(),
        ];

        foreach ($viewers as $viewer) {
            $response = $this->actingAs($viewer)->get(route('boards.show', $board))->assertOk();

            foreach ($tasks as $task) {
                $response->assertSee($task->title);
            }
        }
    }

    public function test_tasks_stay_visible_to_a_developer_after_they_move_to_testing(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::factory()->for($board)->status(TaskStatus::DOING)->create();
        $testing = BoardColumn::factory()->for($board)->status(TaskStatus::TESTING)->create();

        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $testing->id, 'status' => TaskStatus::TESTING, 'assigned_to' => $developer->id]);

        $this->actingAs($developer)->get(route('boards.show', $board))->assertOk()->assertSee($task->title);
    }
}
