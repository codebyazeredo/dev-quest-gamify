<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardColumnManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_rename_and_delete_a_column(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();

        $this->actingAs($admin)->postJson(route('api.columns.store', $board), ['name' => 'Blocked'])->assertCreated();

        $column = $board->columns()->where('name', 'Blocked')->firstOrFail();

        $this->assertNull($column->status);

        $this->actingAs($admin)->putJson(route('api.columns.rename', $column), ['name' => 'On Hold'])->assertOk();

        $this->assertSame('On Hold', $column->refresh()->name);

        $this->actingAs($admin)->deleteJson(route('api.columns.destroy', $column))->assertOk();

        $this->assertNull(BoardColumn::find($column->id));
    }

    public function test_a_new_column_is_untagged_and_has_no_scoring_effect(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();

        $this->actingAs($admin)->postJson(route('api.columns.store', $board), ['name' => 'A Fazer (livre)'])->assertCreated();

        $column = $board->columns()->where('name', 'A Fazer (livre)')->firstOrFail();

        $this->assertNull($column->status);
        $this->assertFalse($column->is_final);
    }

    public function test_admin_can_tag_a_column_with_a_milestone(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->untagged()->create();

        $this->actingAs($admin)
            ->putJson(route('api.columns.milestone', $column), ['status' => TaskStatus::DONE->value])
            ->assertOk();

        $column->refresh();

        $this->assertSame(TaskStatus::DONE, $column->status);
        $this->assertTrue($column->is_final);
    }

    public function test_tagging_a_second_column_with_the_same_milestone_is_blocked(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        BoardColumn::factory()->for($board)->status(TaskStatus::DONE)->create();
        $other = BoardColumn::factory()->for($board)->untagged()->create();

        $this->actingAs($admin)
            ->putJson(route('api.columns.milestone', $other), ['status' => TaskStatus::DONE->value])
            ->assertStatus(409)
            ->assertJsonPath('message', 'Este quadro já tem uma coluna marcada com este marco.');

        $this->assertNull($other->refresh()->status);
    }

    public function test_deleting_a_column_with_tasks_is_blocked(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->untagged()->create();
        Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id, 'status' => null]);

        $this->actingAs($admin)
            ->deleteJson(route('api.columns.destroy', $column))
            ->assertStatus(409)
            ->assertJsonPath('message', 'Esta coluna ainda possui tarefas.');

        $this->assertNotNull(BoardColumn::find($column->id));
    }

    public function test_admin_can_reorder_columns(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $first = BoardColumn::factory()->for($board)->untagged()->create(['position' => 0]);
        $second = BoardColumn::factory()->for($board)->untagged()->create(['position' => 1]);

        $this->actingAs($admin)->postJson(route('api.columns.move-up', $second))->assertOk();

        $this->assertSame(0, $second->refresh()->position);
        $this->assertSame(1, $first->refresh()->position);
    }

    public function test_a_developer_cannot_manage_columns(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();

        $this->actingAs($developer)->postJson(route('api.columns.store', $board), ['name' => 'Blocked'])->assertForbidden();
    }
}
