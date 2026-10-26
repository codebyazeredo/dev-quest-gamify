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
            $component = Livewire::actingAs($viewer)->test(Kanban::class, ['board' => $board]);

            foreach ($tasks as $task) {
                $component->assertSee($task->title);
            }
        }
    }

    public function test_tasks_stay_visible_to_a_developer_after_they_move_to_testing(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $doing = BoardColumn::factory()->for($board)->status(TaskStatus::DOING)->create();
        $testing = BoardColumn::factory()->for($board)->status(TaskStatus::TESTING)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $testing->id, 'status' => TaskStatus::TESTING, 'assigned_to' => $developer->id]);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->assertSee($task->title);

        $this->assertNotNull($doing);
    }
}
