<?php

namespace Tests\Feature\Admin;

use App\Enums\ChallengeType;
use App\Livewire\Admin\Challenges;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChallengeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_challenge(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)
            ->test(Challenges::class)
            ->set('name', 'Sprint Push')
            ->set('type', ChallengeType::TASKS_COMPLETED->value)
            ->set('target', 3)
            ->set('xp_reward', 40)
            ->call('create');

        $challenge = Challenge::where('name', 'Sprint Push')->first();
        $this->assertNotNull($challenge);

        $component->call('edit', $challenge->id)
            ->set('editingName', 'Sprint Push II')
            ->call('update');

        $this->assertSame('Sprint Push II', $challenge->refresh()->name);

        $component->call('delete', $challenge->id);
        $this->assertNull(Challenge::find($challenge->id));
    }

    public function test_developer_gets_forbidden_on_the_route(): void
    {
        $developer = User::factory()->developer()->create();

        $response = $this->actingAs($developer)->get('/admin/challenges');

        $response->assertForbidden();
    }

    public function test_livewire_method_rejects_non_admin_even_when_route_middleware_is_bypassed(): void
    {
        $developer = User::factory()->developer()->create();

        Livewire::actingAs($developer)
            ->test(Challenges::class)
            ->assertForbidden();
    }
}
