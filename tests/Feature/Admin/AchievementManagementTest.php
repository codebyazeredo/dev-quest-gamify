<?php

namespace Tests\Feature\Admin;

use App\Enums\AchievementConditionType;
use App\Livewire\Admin\Achievements\Create;
use App\Livewire\Admin\Achievements\Edit;
use App\Livewire\Admin\Achievements\Index;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AchievementManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_an_achievement(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'Night Owl')
            ->set('condition_type', AchievementConditionType::TASKS_COMPLETED_TOTAL->value)
            ->set('condition_value', 3)
            ->set('xp_reward', 30)
            ->call('save');

        $achievement = Achievement::where('name', 'Night Owl')->first();
        $this->assertNotNull($achievement);

        Livewire::actingAs($admin)
            ->test(Edit::class, ['achievementId' => $achievement->id])
            ->set('name', 'Night Owl Supreme')
            ->call('save');

        $this->assertSame('Night Owl Supreme', $achievement->refresh()->name);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('delete', $achievement->id);

        $this->assertNull(Achievement::find($achievement->id));
    }

    public function test_index_paginates_the_list(): void
    {
        $admin = User::factory()->admin()->create();
        Achievement::factory()->count(20)->create();

        $achievements = Livewire::actingAs($admin)->test(Index::class)->viewData('achievements');

        $this->assertSame(10, $achievements->count());
        $this->assertTrue($achievements->hasMorePages());
    }

    public function test_developer_gets_forbidden_on_the_route(): void
    {
        $developer = User::factory()->developer()->create();

        $response = $this->actingAs($developer)->get('/admin/achievements');

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
