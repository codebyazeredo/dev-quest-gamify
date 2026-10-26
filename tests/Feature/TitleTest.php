<?php

namespace Tests\Feature;

use App\Models\Title;
use App\Models\User;
use Database\Seeders\TitleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TitleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TitleSeeder::class);
    }

    public function test_admin_sees_every_title_as_available_and_can_select_any_of_them(): void
    {
        $admin = User::factory()->admin()->create();
        $title = Title::first();

        $this->actingAs($admin)->get(route('titles'))
            ->assertOk()
            ->assertViewHas('unlocked', fn ($shown) => $shown->count() === Title::count());

        $this->actingAs($admin)->postJson(route('api.me.titles.select', $title))->assertOk();

        $this->assertSame($title->id, $admin->fresh()->selected_title_id);
    }

    public function test_a_user_can_clear_the_selected_title(): void
    {
        $admin = User::factory()->admin()->create(['selected_title_id' => Title::first()->id]);

        $this->actingAs($admin)->deleteJson(route('api.me.titles.clear'))->assertOk();

        $this->assertNull($admin->fresh()->selected_title_id);
    }

    public function test_a_developer_with_no_unlocked_titles_sees_none(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get(route('titles'))
            ->assertOk()
            ->assertViewHas('unlocked', fn ($shown) => $shown->isEmpty());
    }

    public function test_a_developer_cannot_select_a_title_they_have_not_unlocked(): void
    {
        $developer = User::factory()->developer()->create();
        $title = Title::first();

        $this->actingAs($developer)->postJson(route('api.me.titles.select', $title))->assertForbidden();

        $this->assertNull($developer->fresh()->selected_title_id);
    }
}
