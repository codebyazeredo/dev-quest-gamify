<?php

namespace Tests\Unit\Services;

use App\Enums\ChallengeType;
use App\Models\Challenge;
use App\Models\Level;
use App\Models\User;
use App\Models\UserChallenge;
use App\Services\ChallengeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChallengeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
    }

    public function test_first_progress_lazily_creates_the_user_challenge(): void
    {
        $user = User::factory()->create();
        $challenge = Challenge::factory()->create([
            'type' => ChallengeType::TASKS_COMPLETED,
            'target' => 5,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $this->assertDatabaseCount('user_challenges', 0);

        app(ChallengeService::class)->recordProgress($user, ChallengeType::TASKS_COMPLETED, now());

        $userChallenge = UserChallenge::where('user_id', $user->id)->where('challenge_id', $challenge->id)->first();
        $this->assertNotNull($userChallenge);
        $this->assertSame(1, $userChallenge->progress);
    }

    public function test_progress_completes_the_challenge_and_grants_xp_once(): void
    {
        $user = User::factory()->create();
        $challenge = Challenge::factory()->create([
            'type' => ChallengeType::TASKS_COMPLETED,
            'target' => 2,
            'xp_reward' => 75,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $service = app(ChallengeService::class);
        $service->recordProgress($user, ChallengeType::TASKS_COMPLETED, now());
        $service->recordProgress($user, ChallengeType::TASKS_COMPLETED, now());

        $service->recordProgress($user, ChallengeType::TASKS_COMPLETED, now());

        $userChallenge = UserChallenge::where('user_id', $user->id)->where('challenge_id', $challenge->id)->first();

        $this->assertSame(2, $userChallenge->progress);
        $this->assertNotNull($userChallenge->completed_at);
        $this->assertSame(75, $user->xpTransactions()->sum('amount'));
    }

    public function test_progress_outside_the_active_window_is_ignored(): void
    {
        $user = User::factory()->create();
        Challenge::factory()->create([
            'type' => ChallengeType::TASKS_COMPLETED,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDays(5),
        ]);

        app(ChallengeService::class)->recordProgress($user, ChallengeType::TASKS_COMPLETED, now());

        $this->assertDatabaseCount('user_challenges', 0);
    }

    public function test_progress_of_a_different_type_does_not_affect_this_challenge(): void
    {
        $user = User::factory()->create();
        Challenge::factory()->create([
            'type' => ChallengeType::BUGS_RESOLVED,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        app(ChallengeService::class)->recordProgress($user, ChallengeType::TASKS_COMPLETED, now());

        $this->assertDatabaseCount('user_challenges', 0);
    }
}
