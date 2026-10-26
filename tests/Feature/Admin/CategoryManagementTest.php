<?php

namespace Tests\Feature\Admin;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_PAYLOAD = ['name' => 'Spike', 'base_points' => 8, 'color' => '#dbeafe', 'text_color' => '#1e3a8a'];

    public function test_admin_can_create_update_and_delete_a_category(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.categories.store'), self::VALID_PAYLOAD)
            ->assertCreated()
            ->assertJsonPath('data.name', 'Spike')
            ->assertJsonPath('data.color', '#dbeafe');

        $category = TaskCategory::where('name', 'Spike')->firstOrFail();

        $this->actingAs($admin)->putJson(route('api.categories.update', $category), [...self::VALID_PAYLOAD, 'name' => 'Research Spike'])
            ->assertOk();

        $this->assertSame('Research Spike', $category->refresh()->name);

        $this->actingAs($admin)->deleteJson(route('api.categories.destroy', $category))->assertOk();

        $this->assertNull(TaskCategory::find($category->id));
    }

    public function test_invalid_data_is_rejected_before_reaching_the_service(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.categories.store'), [...self::VALID_PAYLOAD, 'color' => 'azul'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('color');

        $this->assertSame(0, TaskCategory::count());
    }

    public function test_category_with_tasks_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $category = TaskCategory::factory()->create();
        Task::factory()->create(['category_id' => $category->id]);

        $this->actingAs($admin)->deleteJson(route('api.categories.destroy', $category))
            ->assertStatus(409)
            ->assertJsonPath('message', 'Esta categoria ainda possui tarefas.');

        $this->assertNotNull(TaskCategory::find($category->id));
    }

    public function test_non_admin_users_cannot_manage_categories(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->getJson(route('api.categories.index'))->assertForbidden();
        $this->actingAs($developer)->postJson(route('api.categories.store'), self::VALID_PAYLOAD)->assertForbidden();
    }

    public function test_guests_receive_an_unauthenticated_response(): void
    {
        $this->getJson(route('api.categories.index'))->assertUnauthorized();
    }
}
