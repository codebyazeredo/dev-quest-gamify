<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Livewire\Task\Kanban;
use App\Livewire\Task\Show;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskMovementHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_moving_a_task_between_columns_records_a_movement(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $destination = BoardColumn::factory()->for($board)->status(TaskStatus::DOING)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $origin->id, 'status' => TaskStatus::TODO]);

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $destination->id, 0);

        $movement = TaskMovement::where('task_id', $task->id)->latest('id')->first();
        $this->assertNotNull($movement);
        $this->assertSame($origin->id, $movement->from_column_id);
        $this->assertSame($destination->id, $movement->to_column_id);
        $this->assertSame($admin->id, $movement->user_id);
    }

    public function test_reordering_within_the_same_column_does_not_record_a_movement(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id, 'status' => TaskStatus::TODO, 'position' => 0]);
        Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id, 'status' => TaskStatus::TODO, 'position' => 1]);

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $column->id, 1);

        $this->assertSame(0, TaskMovement::where('task_id', $task->id)->count());
    }

    public function test_rejection_note_is_stored_on_the_movement_and_visible_on_the_task_page(): void
    {
        $tester = User::factory()->tester()->create();
        $assignee = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $testing = $board->columns->firstWhere('status', TaskStatus::TESTING);
        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $testing->id,
            'status' => TaskStatus::TESTING,
            'assigned_to' => $assignee->id,
        ]);

        Livewire::actingAs($tester)
            ->test(Show::class, ['task' => $task])
            ->set('rejectionReasonInput', 'Faltou tratar o caso de borda')
            ->call('reject');

        $movement = TaskMovement::where('task_id', $task->id)->latest('id')->first();
        $this->assertSame('Faltou tratar o caso de borda', $movement->note);

        Livewire::actingAs($assignee)
            ->test(Show::class, ['task' => $task])
            ->assertSee('Faltou tratar o caso de borda');
    }

    public function test_movement_history_is_visible_to_any_authenticated_user(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $origin = BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create(['name' => 'A Fazer']);
        $destination = BoardColumn::factory()->for($board)->status(TaskStatus::DOING)->create(['name' => 'Em Andamento']);
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $origin->id, 'status' => TaskStatus::TODO]);

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $destination->id, 0);

        Livewire::actingAs($viewer)
            ->test(Show::class, ['task' => $task])
            ->assertSee('A Fazer')
            ->assertSee('Em Andamento');
    }
}
