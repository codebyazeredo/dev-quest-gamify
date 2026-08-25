<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Livewire\Board\Edit;
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
        BoardColumn::seedDefaultsFor($board);

        $component = Livewire::actingAs($admin)
            ->test(Edit::class, ['board' => $board])
            ->set('newColumnName', 'Blocked')
            ->set('newColumnStatus', TaskStatus::TODO->value)
            ->call('addColumn');

        $column = $board->columns()->where('name', 'Blocked')->first();
        $this->assertNotNull($column);

        $component->call('renameColumn', $column->id, 'On Hold');
        $this->assertSame('On Hold', $column->refresh()->name);

        $component->call('deleteColumn', $column->id);
        $this->assertNull(BoardColumn::find($column->id));
    }

    public function test_deleting_a_column_with_tasks_is_blocked(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $column = $board->columns()->first();
        Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id]);

        Livewire::actingAs($admin)
            ->test(Edit::class, ['board' => $board])
            ->call('deleteColumn', $column->id)
            ->assertHasErrors('columns');

        $this->assertNotNull(BoardColumn::find($column->id));
    }
}
