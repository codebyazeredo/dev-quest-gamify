<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Titles;
use App\Models\Title;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TitleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_title(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)
            ->test(Titles::class)
            ->set('name', 'Night Owl')
            ->set('icon', '🦉')
            ->call('create');

        $title = Title::where('name', 'Night Owl')->first();
        $this->assertNotNull($title);

        $component->call('edit', $title->id)
            ->set('editingName', 'Night Owl Supreme')
            ->call('update');

        $this->assertSame('Night Owl Supreme', $title->refresh()->name);

        $component->call('delete', $title->id);
        $this->assertNull(Title::find($title->id));
    }

    public function test_developer_gets_forbidden_on_the_route(): void
    {
        $developer = User::factory()->developer()->create();

        $response = $this->actingAs($developer)->get('/admin/titles');

        $response->assertForbidden();
    }

    public function test_livewire_method_rejects_non_admin_even_when_route_middleware_is_bypassed(): void
    {
        $developer = User::factory()->developer()->create();

        Livewire::actingAs($developer)
            ->test(Titles::class)
            ->assertForbidden();
    }
}
