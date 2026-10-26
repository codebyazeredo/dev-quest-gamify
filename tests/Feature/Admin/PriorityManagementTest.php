<?php

namespace Tests\Feature\Admin;

use App\Models\Task;
use App\Models\TaskPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriorityManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_priority(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.priorities.store'), ['name' => 'Bloqueante', 'multiplier' => '8.00'])
            ->assertCreated();

        $priority = TaskPriority::where('name', 'Bloqueante')->firstOrFail();

        $this->assertSame('8.00', (string) $priority->multiplier);

        $this->actingAs($admin)
            ->putJson(route('api.priorities.update', $priority), ['name' => 'Bloqueante', 'multiplier' => '9.00'])
            ->assertOk();

        $this->assertSame('9.00', (string) $priority->refresh()->multiplier);

        $this->actingAs($admin)->deleteJson(route('api.priorities.destroy', $priority))->assertOk();

        $this->assertNull(TaskPriority::find($priority->id));
    }

    public function test_deleting_a_priority_in_use_by_a_task_is_blocked(): void
    {
        $admin = User::factory()->admin()->create();
        $priority = TaskPriority::factory()->create();
        Task::factory()->create(['priority_id' => $priority->id]);

        $this->actingAs($admin)->deleteJson(route('api.priorities.destroy', $priority))
            ->assertStatus(409)
            ->assertJsonPath('message', 'Esta gravidade ainda possui tarefas.');

        $this->assertNotNull(TaskPriority::find($priority->id));
    }

    public function test_index_paginates_the_list(): void
    {
        $admin = User::factory()->admin()->create();
        TaskPriority::factory()->count(20)->create();

        $this->actingAs($admin)->getJson(route('api.priorities.index'))
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.last_page', 2);
    }

    public function test_developer_is_forbidden_on_the_page_and_on_the_api(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/admin/priority-rules')->assertForbidden();
        $this->actingAs($developer)->getJson(route('api.priorities.index'))->assertForbidden();
    }
}
