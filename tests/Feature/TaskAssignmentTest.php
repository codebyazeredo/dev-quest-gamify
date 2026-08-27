<?php

namespace Tests\Feature;

use App\Livewire\Task\Kanban;
use App\Livewire\Task\Show;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_developer_can_claim_unassigned_task(): void
    {
        $developer = User::factory()->developer()->create();
        [$board, $column] = $this->boardWithColumn();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id, 'assigned_to' => null]);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->call('claim', $task->id);

        $this->assertSame($developer->id, $task->refresh()->assigned_to);
    }

    public function test_developer_cannot_claim_an_already_assigned_task(): void
    {
        $developer = User::factory()->developer()->create();
        $otherDeveloper = User::factory()->developer()->create();
        [$board, $column] = $this->boardWithColumn();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id, 'assigned_to' => $otherDeveloper->id]);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->call('claim', $task->id)
            ->assertForbidden();
    }

    public function test_admin_can_assign_task_to_any_user(): void
    {
        $admin = User::factory()->admin()->create();
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['assigned_to' => null]);

        Livewire::actingAs($admin)
            ->test(Show::class, ['task' => $task])
            ->set('assignToUserId', $developer->id)
            ->call('assignTo');

        $this->assertSame($developer->id, $task->refresh()->assigned_to);
    }

    public function test_product_owner_cannot_arbitrarily_reassign_a_task(): void
    {
        $po = User::factory()->productOwner()->create();
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['assigned_to' => null]);

        Livewire::actingAs($po)
            ->test(Show::class, ['task' => $task])
            ->set('assignToUserId', $developer->id)
            ->call('assignTo')
            ->assertForbidden();
    }

    protected function boardWithColumn(): array
    {
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->create();

        return [$board, $column];
    }
}
