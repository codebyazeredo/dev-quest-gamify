<?php

namespace Tests\Feature\Admin;

use App\Enums\ChallengeType;
use App\Livewire\Admin\Challenges\Create;
use App\Livewire\Admin\Challenges\Edit;
use App\Livewire\Admin\Challenges\Index;
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

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'Sprint Push')
            ->set('type', ChallengeType::TASKS_COMPLETED->value)
            ->set('target', 3)
            ->set('xp_reward', 40)
            ->call('save');

        $challenge = Challenge::where('name', 'Sprint Push')->first();
        $this->assertNotNull($challenge);

        Livewire::actingAs($admin)
            ->test(Edit::class, ['challengeId' => $challenge->id])
            ->set('name', 'Sprint Push II')
            ->call('save');

        $this->assertSame('Sprint Push II', $challenge->refresh()->name);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('delete', $challenge->id);

        $this->assertNull(Challenge::find($challenge->id));
    }

    public function test_index_paginates_the_list(): void
    {
        $admin = User::factory()->admin()->create();
        Challenge::factory()->count(20)->create();

        $challenges = Livewire::actingAs($admin)->test(Index::class)->viewData('challenges');

        $this->assertSame(15, $challenges->count());
        $this->assertTrue($challenges->hasMorePages());
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
            ->test(Index::class)
            ->assertForbidden();
    }
}
