<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Titles\Create;
use App\Livewire\Admin\Titles\Edit;
use App\Livewire\Admin\Titles\Index;
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

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'Night Owl')
            ->set('icon', 'owl')
            ->call('save');

        $title = Title::where('name', 'Night Owl')->first();
        $this->assertNotNull($title);

        Livewire::actingAs($admin)
            ->test(Edit::class, ['titleId' => $title->id])
            ->set('name', 'Night Owl Supreme')
            ->call('save');

        $this->assertSame('Night Owl Supreme', $title->refresh()->name);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('delete', $title->id);

        $this->assertNull(Title::find($title->id));
    }

    public function test_index_paginates_the_list(): void
    {
        $admin = User::factory()->admin()->create();
        Title::factory()->count(20)->create();

        $titles = Livewire::actingAs($admin)->test(Index::class)->viewData('titles');

        $this->assertSame(10, $titles->count());
        $this->assertTrue($titles->hasMorePages());
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
            ->test(Index::class)
            ->assertForbidden();
    }
}
