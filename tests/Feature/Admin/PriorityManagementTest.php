<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Priorities\Create;
use App\Livewire\Admin\Priorities\Edit;
use App\Livewire\Admin\Priorities\Index;
use App\Models\Task;
use App\Models\TaskPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PriorityManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_priority(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'Bloqueante')
            ->set('multiplier', '8.00')
            ->call('save');

        $priority = TaskPriority::where('name', 'Bloqueante')->first();
        $this->assertNotNull($priority);
        $this->assertSame('8.00', (string) $priority->multiplier);

        Livewire::actingAs($admin)
            ->test(Edit::class, ['priorityId' => $priority->id])
            ->set('multiplier', '9.00')
            ->call('save');

        $this->assertSame('9.00', (string) $priority->refresh()->multiplier);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('delete', $priority->id);

        $this->assertNull(TaskPriority::find($priority->id));
    }

    public function test_deleting_a_priority_in_use_by_a_task_is_blocked(): void
    {
        $admin = User::factory()->admin()->create();
        $priority = TaskPriority::factory()->create();
        Task::factory()->create(['priority_id' => $priority->id]);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('delete', $priority->id)
            ->assertHasErrors(['delete']);

        $this->assertNotNull(TaskPriority::find($priority->id));
    }

    public function test_index_paginates_the_list(): void
    {
        $admin = User::factory()->admin()->create();
        TaskPriority::factory()->count(20)->create();

        $priorities = Livewire::actingAs($admin)->test(Index::class)->viewData('priorities');

        $this->assertSame(10, $priorities->count());
        $this->assertTrue($priorities->hasMorePages());
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
            ->test(Index::class)
            ->assertForbidden();
    }
}
