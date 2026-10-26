<?php

namespace Tests\Feature\Admin;

use App\Enums\AchievementConditionType;
use App\Models\Achievement;
use App\Models\Title;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementManagementTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Night Owl',
            'icon' => 'trophy',
            'condition_type' => AchievementConditionType::TASKS_COMPLETED_TOTAL->value,
            'condition_value' => 3,
            'xp_reward' => 30,
            ...$overrides,
        ];
    }

    public function test_admin_can_create_update_and_delete_an_achievement(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.achievements.store'), $this->payload())->assertCreated();

        $achievement = Achievement::where('name', 'Night Owl')->firstOrFail();

        $this->actingAs($admin)
            ->putJson(route('api.achievements.update', $achievement), $this->payload(['name' => 'Night Owl Supreme', 'active' => true]))
            ->assertOk();

        $this->assertSame('Night Owl Supreme', $achievement->refresh()->name);

        $this->actingAs($admin)->deleteJson(route('api.achievements.destroy', $achievement))->assertOk();

        $this->assertNull(Achievement::find($achievement->id));
    }

    public function test_admin_can_create_an_achievement_that_awards_a_title(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.achievements.store'), $this->payload(['award_title' => true, 'reward_title_name' => 'Coruja']))
            ->assertCreated();

        $this->assertSame(Achievement::where('name', 'Night Owl')->value('id'), Title::where('name', 'Coruja')->value('achievement_id'));
    }

    public function test_reward_title_name_is_required_when_awarding_a_title(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.achievements.store'), $this->payload(['award_title' => true]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('reward_title_name');
    }

    public function test_admin_can_toggle_and_duplicate_an_achievement(): void
    {
        $admin = User::factory()->admin()->create();
        $achievement = Achievement::factory()->create(['active' => true]);

        $this->actingAs($admin)->postJson(route('api.achievements.toggle-active', $achievement))->assertOk();
        $this->assertFalse($achievement->refresh()->active);

        $this->actingAs($admin)->postJson(route('api.achievements.duplicate', $achievement))->assertCreated();
        $this->assertSame(2, Achievement::count());
    }

    public function test_index_paginates_the_list(): void
    {
        $admin = User::factory()->admin()->create();
        Achievement::factory()->count(20)->create();

        $this->actingAs($admin)->getJson(route('api.achievements.index'))
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.last_page', 2);
    }

    public function test_developer_gets_forbidden_on_the_page_and_on_the_api(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/admin/achievements')->assertForbidden();
        $this->actingAs($developer)->getJson(route('api.achievements.index'))->assertForbidden();
    }
}
