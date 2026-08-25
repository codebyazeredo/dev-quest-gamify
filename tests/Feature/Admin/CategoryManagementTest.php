<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Categories;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_category(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)
            ->test(Categories::class)
            ->set('name', 'Spike')
            ->set('base_points', 8)
            ->call('create');

        $category = TaskCategory::where('name', 'Spike')->first();
        $this->assertNotNull($category);

        $component->call('edit', $category->id)
            ->set('editingName', 'Research Spike')
            ->call('update');

        $this->assertSame('Research Spike', $category->refresh()->name);

        $component->call('delete', $category->id);
        $this->assertNull(TaskCategory::find($category->id));
    }

    public function test_developer_gets_forbidden_on_the_route(): void
    {
        $developer = User::factory()->developer()->create();

        $response = $this->actingAs($developer)->get('/admin/categories');

        $response->assertForbidden();
    }

    public function test_livewire_method_rejects_non_admin_even_when_route_middleware_is_bypassed(): void
    {
        $developer = User::factory()->developer()->create();
        $category = TaskCategory::factory()->create();

        Livewire::actingAs($developer)
            ->test(Categories::class)
            ->assertForbidden();
    }
}
