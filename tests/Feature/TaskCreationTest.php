<?php

namespace Tests\Feature;

use App\Livewire\Task\Create;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\TaskCategory;
use App\Models\TaskPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_card_in_a_freeform_column_with_no_milestone_tag(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $freeColumn = BoardColumn::factory()->for($board)->untagged()->create();
        $category = TaskCategory::factory()->create();
        $priority = TaskPriority::factory()->create();

        Livewire::actingAs($admin)
            ->test(Create::class, ['board' => $board, 'columnId' => $freeColumn->id])
            ->set('title', 'Organizar backlog')
            ->set('category_id', $category->id)
            ->set('priority_id', $priority->id)
            ->call('save');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Organizar backlog',
            'column_id' => $freeColumn->id,
            'status' => null,
        ]);
    }

    public function test_mount_aborts_when_the_column_does_not_belong_to_the_board(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $otherBoard = Board::factory()->create();
        $columnFromOtherBoard = BoardColumn::factory()->for($otherBoard)->untagged()->create();

        Livewire::actingAs($admin)
            ->test(Create::class, ['board' => $board, 'columnId' => $columnFromOtherBoard->id])
            ->assertStatus(404);
    }
}
