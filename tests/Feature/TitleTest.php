<?php

namespace Tests\Feature;

use App\Livewire\Gamification\Titles;
use App\Models\Title;
use App\Models\User;
use Database\Seeders\TitleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

        $shown = Livewire::actingAs($admin)->test(Titles::class)->viewData('unlockedTitles');
        $this->assertSame(Title::count(), $shown->count());

        Livewire::actingAs($admin)
            ->test(Titles::class)
            ->call('selectTitle', $title->id);

        $this->assertSame($title->id, $admin->fresh()->selected_title_id);
    }

    public function test_a_developer_with_no_unlocked_titles_sees_none(): void
    {
        $developer = User::factory()->developer()->create();

        $shown = Livewire::actingAs($developer)->test(Titles::class)->viewData('unlockedTitles');

        $this->assertTrue($shown->isEmpty());
    }

    public function test_a_developer_cannot_select_a_title_they_have_not_unlocked(): void
    {
        $developer = User::factory()->developer()->create();
        $title = Title::first();

        Livewire::actingAs($developer)
            ->test(Titles::class)
            ->call('selectTitle', $title->id)
            ->assertForbidden();
    }
}
