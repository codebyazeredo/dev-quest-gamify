<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Categories\Create;
use App\Livewire\Admin\Categories\Edit;
use App\Livewire\Admin\Categories\Index;
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

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'Spike')
            ->set('base_points', 8)
            ->call('save');

        $category = TaskCategory::where('name', 'Spike')->first();
        $this->assertNotNull($category);

        Livewire::actingAs($admin)
            ->test(Edit::class, ['categoryId' => $category->id])
            ->set('name', 'Research Spike')
            ->call('save');

        $this->assertSame('Research Spike', $category->refresh()->name);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('delete', $category->id);

        $this->assertNull(TaskCategory::find($category->id));
    }

    public function test_index_paginates_the_list(): void
    {
        $admin = User::factory()->admin()->create();
        TaskCategory::factory()->count(20)->create();

        $categories = Livewire::actingAs($admin)->test(Index::class)->viewData('categories');

        $this->assertSame(15, $categories->count());
        $this->assertTrue($categories->hasMorePages());
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

        Livewire::actingAs($developer)
            ->test(Index::class)
            ->assertForbidden();
    }
}
