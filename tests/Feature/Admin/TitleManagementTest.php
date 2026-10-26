<?php

namespace Tests\Feature\Admin;

use App\Models\Title;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TitleManagementTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_PAYLOAD = ['name' => 'Night Owl', 'icon' => 'owl', 'unlock_type' => 'achievement'];

    public function test_admin_can_create_update_and_delete_a_title(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.titles.store'), self::VALID_PAYLOAD)->assertCreated();

        $title = Title::where('name', 'Night Owl')->firstOrFail();

        $this->actingAs($admin)
            ->putJson(route('api.titles.update', $title), [...self::VALID_PAYLOAD, 'name' => 'Night Owl Supreme', 'active' => true])
            ->assertOk();

        $this->assertSame('Night Owl Supreme', $title->refresh()->name);

        $this->actingAs($admin)->deleteJson(route('api.titles.destroy', $title))->assertOk();

        $this->assertNull(Title::find($title->id));
    }

    public function test_admin_can_toggle_a_title(): void
    {
        $admin = User::factory()->admin()->create();
        $title = Title::factory()->create(['active' => true]);

        $this->actingAs($admin)->postJson(route('api.titles.toggle-active', $title))->assertOk();

        $this->assertFalse($title->refresh()->active);
    }

    public function test_unlock_rule_fields_are_required_for_the_chosen_type(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.titles.store'), ['name' => 'Maratonista', 'unlock_type' => 'challenges_count'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('challenges_required');
    }

    public function test_index_paginates_the_list(): void
    {
        $admin = User::factory()->admin()->create();
        Title::factory()->count(20)->create();

        $this->actingAs($admin)->getJson(route('api.titles.index'))
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.last_page', 2);
    }

    public function test_developer_gets_forbidden_on_the_page_and_on_the_api(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/admin/titles')->assertForbidden();
        $this->actingAs($developer)->getJson(route('api.titles.index'))->assertForbidden();
    }
}
