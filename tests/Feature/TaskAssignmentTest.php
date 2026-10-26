<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_developer_can_claim_unassigned_task(): void
    {
        $developer = User::factory()->developer()->create();
        [$board, $column] = $this->boardWithColumn();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id, 'assigned_to' => null]);

        $this->actingAs($developer)->postJson(route('api.tasks.claim', $task))->assertOk();

        $this->assertSame($developer->id, $task->refresh()->assigned_to);
    }

    public function test_developer_cannot_claim_an_already_assigned_task(): void
    {
        $developer = User::factory()->developer()->create();
        $otherDeveloper = User::factory()->developer()->create();
        [$board, $column] = $this->boardWithColumn();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id, 'assigned_to' => $otherDeveloper->id]);

        $this->actingAs($developer)->postJson(route('api.tasks.claim', $task))->assertForbidden();

        $this->assertSame($otherDeveloper->id, $task->refresh()->assigned_to);
    }

    public function test_admin_can_assign_task_to_any_user(): void
    {
        $admin = User::factory()->admin()->create();
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['assigned_to' => null]);

        $this->actingAs($admin)->postJson(route('api.tasks.assign', $task), ['user_id' => $developer->id])->assertOk();

        $this->assertSame($developer->id, $task->refresh()->assigned_to);
    }

    public function test_product_owner_cannot_arbitrarily_reassign_a_task(): void
    {
        $po = User::factory()->productOwner()->create();
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['assigned_to' => null]);

        $this->actingAs($po)->postJson(route('api.tasks.assign', $task), ['user_id' => $developer->id])->assertForbidden();

        $this->assertNull($task->refresh()->assigned_to);
    }

    public function test_assigning_requires_an_existing_user(): void
    {
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->create(['assigned_to' => null]);

        $this->actingAs($admin)
            ->postJson(route('api.tasks.assign', $task), ['user_id' => 9999])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('user_id');
    }

    protected function boardWithColumn(): array
    {
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->create();

        return [$board, $column];
    }
}
