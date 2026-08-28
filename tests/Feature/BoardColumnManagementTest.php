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

class BoardColumnManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_rename_and_delete_a_column(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();

        $component = Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->set('newColumnName', 'Blocked')
            ->call('addColumn');

        $column = $board->columns()->where('name', 'Blocked')->first();
        $this->assertNotNull($column);
        $this->assertNull($column->status);

        $component->call('renameColumn', $column->id, 'On Hold');
        $this->assertSame('On Hold', $column->refresh()->name);

        $component->call('deleteColumn', $column->id);
        $this->assertNull(BoardColumn::find($column->id));
    }

    public function test_a_new_column_is_untagged_and_has_no_scoring_effect(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->set('newColumnName', 'A Fazer (livre)')
            ->call('addColumn');

        $column = $board->columns()->where('name', 'A Fazer (livre)')->first();

        $this->assertNull($column->status);
        $this->assertFalse($column->is_final);
    }

    public function test_admin_can_tag_a_column_with_a_milestone(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->untagged()->create();

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('setMilestone', $column->id, TaskStatus::DONE->value);

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

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('setMilestone', $other->id, TaskStatus::DONE->value)
            ->assertDispatched('toast', fn ($name, $params) => $params['toast']['type'] === 'error');

        $this->assertNull($other->refresh()->status);
    }

    public function test_deleting_a_column_with_tasks_is_blocked(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->untagged()->create();
        Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id, 'status' => null]);

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('deleteColumn', $column->id)
            ->assertDispatched('toast', fn ($name, $params) => $params['toast']['type'] === 'error');

        $this->assertNotNull(BoardColumn::find($column->id));
    }

    public function test_admin_can_reorder_columns(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $first = BoardColumn::factory()->for($board)->untagged()->create(['position' => 0]);
        $second = BoardColumn::factory()->for($board)->untagged()->create(['position' => 1]);

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveColumnUp', $second->id);

        $this->assertSame(0, $second->refresh()->position);
        $this->assertSame(1, $first->refresh()->position);
    }
}
