<?php

namespace Tests\Feature;

use App\Enums\ChallengeType;
use App\Enums\TitleUnlockType;
use App\Models\Challenge;
use App\Models\Title;
use App\Models\User;
use App\Models\UserChallenge;
use App\Models\UserTitle;
use App\Services\ChallengeService;
use Database\Seeders\LevelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TitleUnlockRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelSeeder::class);
    }

    private function completeChallenge(User $user, Challenge $challenge): void
    {
        app(ChallengeService::class)->recordProgress($user, $challenge->type, now(), $challenge->target);
    }

    public function test_completing_a_specific_challenge_grants_its_title(): void
    {
        $developer = User::factory()->developer()->create();
        $challenge = Challenge::factory()->create(['type' => ChallengeType::TASKS_COMPLETED, 'target' => 1]);
        $title = Title::factory()->create([
            'unlock_type' => TitleUnlockType::CHALLENGE,
            'challenge_id' => $challenge->id,
        ]);

        $this->completeChallenge($developer, $challenge);

        $this->assertTrue(UserTitle::where('user_id', $developer->id)->where('title_id', $title->id)->exists());
    }

    public function test_title_by_challenge_count_is_granted_only_after_reaching_the_quantity(): void
    {
        $developer = User::factory()->developer()->create();
        $first = Challenge::factory()->create(['type' => ChallengeType::TASKS_COMPLETED, 'target' => 1]);
        $second = Challenge::factory()->create(['type' => ChallengeType::BUGS_RESOLVED, 'target' => 1]);
        $title = Title::factory()->create([
            'unlock_type' => TitleUnlockType::CHALLENGES_COUNT,
            'challenges_required' => 2,
        ]);

        $this->completeChallenge($developer, $first);

        $this->assertFalse(UserTitle::where('title_id', $title->id)->exists());

        $this->completeChallenge($developer, $second);

        $this->assertTrue(UserTitle::where('user_id', $developer->id)->where('title_id', $title->id)->exists());
    }

    public function test_creating_a_title_rewards_users_who_already_met_the_rule(): void
    {
        $developer = User::factory()->developer()->create();
        $challenge = Challenge::factory()->create();

        UserChallenge::create([
            'user_id' => $developer->id,
            'challenge_id' => $challenge->id,
            'progress' => $challenge->target,
            'completed_at' => now(),
        ]);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.titles.store'), [
                'name' => 'Veterano',
                'unlock_type' => TitleUnlockType::CHALLENGES_COUNT->value,
                'challenges_required' => 1,
            ])
            ->assertCreated();

        $title = Title::where('name', 'Veterano')->firstOrFail();

        $this->assertTrue(UserTitle::where('user_id', $developer->id)->where('title_id', $title->id)->exists());
    }

    public function test_title_rule_requires_the_matching_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.titles.store'), ['name' => 'Campeão', 'unlock_type' => TitleUnlockType::CHALLENGE->value])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('challenge_id');

        $this->actingAs($admin)
            ->postJson(route('api.titles.store'), ['name' => 'Maratonista', 'unlock_type' => TitleUnlockType::CHALLENGES_COUNT->value])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('challenges_required');
    }

    public function test_admin_creates_a_challenge_together_with_its_reward_title(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.challenges.store'), [
                'name' => 'Sprint Perfeita',
                'type' => ChallengeType::TASKS_COMPLETED->value,
                'target' => 5,
                'xp_reward' => 10,
                'starts_at' => now()->startOfWeek()->format('Y-m-d\TH:i'),
                'ends_at' => now()->endOfWeek()->format('Y-m-d\TH:i'),
                'award_title' => true,
                'reward_title_name' => 'Mestre da Sprint',
            ])
            ->assertCreated();

        $challenge = Challenge::where('name', 'Sprint Perfeita')->firstOrFail();
        $title = Title::where('name', 'Mestre da Sprint')->firstOrFail();

        $this->assertSame(TitleUnlockType::CHALLENGE, $title->unlock_type);
        $this->assertSame($challenge->id, $title->challenge_id);
    }

    public function test_reward_title_name_is_required_when_the_option_is_enabled(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.challenges.store'), [
                'name' => 'Sem Título',
                'type' => ChallengeType::TASKS_COMPLETED->value,
                'target' => 5,
                'xp_reward' => 10,
                'starts_at' => now()->startOfWeek()->format('Y-m-d\TH:i'),
                'ends_at' => now()->endOfWeek()->format('Y-m-d\TH:i'),
                'award_title' => true,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('reward_title_name');
    }

    public function test_duplicating_a_challenge_creates_an_inactive_copy_for_the_next_period(): void
    {
        $admin = User::factory()->admin()->create();
        $challenge = Challenge::factory()->create([
            'name' => 'Desafio Semanal',
            'starts_at' => now()->startOfWeek(),
            'ends_at' => now()->endOfWeek(),
        ]);

        $this->actingAs($admin)->postJson(route('api.challenges.duplicate', $challenge))->assertCreated();

        $copy = Challenge::where('name', 'Desafio Semanal (cópia)')->firstOrFail();

        $this->assertFalse($copy->active);
        $this->assertTrue($copy->starts_at->greaterThan($challenge->ends_at));
        $this->assertSame($challenge->target, $copy->target);
    }

    public function test_admin_dashboard_is_available_only_to_admins(): void
    {
        $admin = User::factory()->admin()->create();
        $developer = User::factory()->developer()->create();

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
        $this->actingAs($developer)->get('/admin/dashboard')->assertForbidden();
    }
}
