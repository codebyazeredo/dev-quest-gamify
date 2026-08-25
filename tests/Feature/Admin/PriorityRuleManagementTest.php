<?php

namespace Tests\Feature\Admin;

use App\Enums\TaskPriority;
use App\Livewire\Admin\PriorityRules;
use App\Livewire\Task\Create;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskPriorityRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PriorityRuleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_a_priority_multiplier(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(PriorityRules::class)
            ->call('edit', TaskPriority::CRITICAL->value)
            ->set('editingMultiplier', '10.00')
            ->call('update');

        $this->assertSame('10.00', (string) TaskPriorityRule::where('priority', TaskPriority::CRITICAL)->first()->multiplier);
    }

    public function test_updated_multiplier_is_frozen_onto_new_tasks(): void
    {
        $admin = User::factory()->admin()->create();
        $po = User::factory()->productOwner()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->create();
        $category = TaskCategory::factory()->create(['base_points' => 10]);

        Livewire::actingAs($admin)
            ->test(PriorityRules::class)
            ->call('edit', TaskPriority::CRITICAL->value)
            ->set('editingMultiplier', '9.00')
            ->call('update');

        Livewire::actingAs($po)
            ->test(Create::class, ['board' => $board, 'columnId' => $column->id])
            ->set('title', 'Critical fix')
            ->set('category_id', $category->id)
            ->set('priority', TaskPriority::CRITICAL->value)
            ->call('save');

        $task = Task::where('title', 'Critical fix')->first();

        $this->assertNotNull($task);
        $this->assertSame('9.00', (string) $task->priority_multiplier);
        $this->assertSame(90, $task->xpValue());
    }

    public function test_developer_is_forbidden_on_the_route(): void
    {
        $developer = User::factory()->developer()->create();

        $response = $this->actingAs($developer)->get('/admin/priority-rules');

        $response->assertForbidden();
    }

    public function test_livewire_method_rejects_non_admin_even_when_route_middleware_is_bypassed(): void
    {
        $developer = User::factory()->developer()->create();

        Livewire::actingAs($developer)
            ->test(PriorityRules::class)
            ->assertForbidden();
    }
}
