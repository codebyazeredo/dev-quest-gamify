<?php

namespace Tests\Feature\Admin;

use App\Enums\AchievementConditionType;
use App\Livewire\Admin\Achievements;
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

        $component = Livewire::actingAs($admin)
            ->test(Achievements::class)
            ->set('name', 'Night Owl')
            ->set('condition_type', AchievementConditionType::TASKS_COMPLETED_TOTAL->value)
            ->set('condition_value', 3)
            ->set('xp_reward', 30)
            ->call('create');

        $achievement = Achievement::where('name', 'Night Owl')->first();
        $this->assertNotNull($achievement);

        $component->call('edit', $achievement->id)
            ->set('editingName', 'Night Owl Supreme')
            ->call('update');

        $this->assertSame('Night Owl Supreme', $achievement->refresh()->name);

        $component->call('delete', $achievement->id);
        $this->assertNull(Achievement::find($achievement->id));
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
            ->test(Achievements::class)
            ->assertForbidden();
    }
}
