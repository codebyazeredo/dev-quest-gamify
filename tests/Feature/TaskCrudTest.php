<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Livewire\Task\Create;
use App\Livewire\Task\Edit;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_owner_can_create_task_with_frozen_points(): void
    {
        $po = User::factory()->productOwner()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->status(TaskStatus::BACKLOG)->create();
        $category = TaskCategory::factory()->create(['base_points' => 20]);
        $priority = TaskPriority::factory()->create(['multiplier' => '2.00']);

        Livewire::actingAs($po)
            ->test(Create::class, ['board' => $board, 'columnId' => $column->id])
            ->set('title', 'Build report')
            ->set('category_id', $category->id)
            ->set('priority_id', $priority->id)
            ->call('save');

        $task = Task::where('title', 'Build report')->first();

        $this->assertNotNull($task);
        $this->assertSame(20, $task->base_points);
        $this->assertSame('2.00', (string) $task->priority_multiplier);
    }

    public function test_developer_cannot_create_task(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->create();

        Livewire::actingAs($developer)
            ->test(Create::class, ['board' => $board, 'columnId' => $column->id])
            ->assertForbidden();
    }

    public function test_product_owner_can_edit_task_details(): void
    {
        $po = User::factory()->productOwner()->create();
        $task = Task::factory()->create(['title' => 'Old title']);

        Livewire::actingAs($po)
            ->test(Edit::class, ['taskId' => $task->id])
            ->set('title', 'New title')
            ->call('save');

        $this->assertSame('New title', $task->refresh()->title);
    }

    public function test_completed_task_blocks_category_and_priority_edit(): void
    {
        $po = User::factory()->productOwner()->create();
        $category = TaskCategory::factory()->create(['base_points' => 10]);
        $otherCategory = TaskCategory::factory()->create(['base_points' => 99]);
        $priority = TaskPriority::factory()->create(['multiplier' => '1.00']);
        $otherPriority = TaskPriority::factory()->create(['multiplier' => '5.00']);
        $task = Task::factory()->create([
            'category_id' => $category->id,
            'priority_id' => $priority->id,
            'base_points' => 10,
            'priority_multiplier' => 1.00,
            'completed_at' => now(),
        ]);

        Livewire::actingAs($po)
            ->test(Edit::class, ['taskId' => $task->id])
            ->set('category_id', $otherCategory->id)
            ->set('priority_id', $otherPriority->id)
            ->call('save');

        $task->refresh();
        $this->assertSame($category->id, $task->category_id);
        $this->assertSame(10, $task->base_points);
        $this->assertSame('1.00', (string) $task->priority_multiplier);
    }
}
