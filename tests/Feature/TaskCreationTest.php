<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\TaskCategory;
use App\Models\TaskPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $this->actingAs($admin)
            ->postJson(route('api.tasks.store'), [
                'column_id' => $freeColumn->id,
                'title' => 'Organizar backlog',
                'category_id' => $category->id,
                'priority_id' => $priority->id,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('tasks', [
            'title' => 'Organizar backlog',
            'column_id' => $freeColumn->id,
            'status' => null,
        ]);
    }

    public function test_a_column_that_does_not_exist_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $category = TaskCategory::factory()->create();
        $priority = TaskPriority::factory()->create();

        $this->actingAs($admin)
            ->postJson(route('api.tasks.store'), [
                'column_id' => 9999,
                'title' => 'Sem coluna',
                'category_id' => $category->id,
                'priority_id' => $priority->id,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('column_id');
    }
}
