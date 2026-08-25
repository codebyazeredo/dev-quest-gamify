<?php

namespace Tests\Unit\Services;

use App\Models\DailyCheckin;
use App\Models\Level;
use App\Models\User;
use App\Services\CheckinService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CheckinServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_checkin_is_idempotent_for_the_same_day(): void
    {
        $user = User::factory()->create();
        $service = app(CheckinService::class);

        $first = $service->checkIn($user);
        $second = $service->checkIn($user);

        $this->assertSame($first->id, $second->id);
        $this->assertDatabaseCount('daily_checkins', 1);
        $this->assertSame(1, $user->xpTransactions()->sum('amount'));
    }

    public function test_streak_continues_on_consecutive_days_and_resets_after_a_gap(): void
    {
        $user = User::factory()->create();
        $service = app(CheckinService::class);

        Carbon::setTestNow('2026-01-01');
        $day1 = $service->checkIn($user);
        $this->assertSame(1, $day1->streak_count);

        Carbon::setTestNow('2026-01-02');
        $day2 = $service->checkIn($user);
        $this->assertSame(2, $day2->streak_count);

        Carbon::setTestNow('2026-01-04'); // gap: skipped 2026-01-03
        $day4 = $service->checkIn($user);
        $this->assertSame(1, $day4->streak_count);
    }

    public function test_streak_bonus_fires_once_per_five_day_run_and_again_after_a_reset(): void
    {
        $user = User::factory()->create();
        $service = app(CheckinService::class);

        Carbon::setTestNow('2026-01-01');
        for ($i = 0; $i < 5; $i++) {
            $service->checkIn($user);
            Carbon::setTestNow(now()->addDay());
        }

        // 5 daily +1 XP grants, plus one +5 bonus on day 5 = 10 XP
        $this->assertSame(10, $user->xpTransactions()->sum('amount'));

        Carbon::setTestNow(now()->addDays(3)); // break the streak
        $service->checkIn($user);
        $this->assertSame(11, $user->xpTransactions()->sum('amount'));

        for ($i = 0; $i < 4; $i++) {
            Carbon::setTestNow(now()->addDay());
            $service->checkIn($user);
        }

        // second streak reaches 5 again: 11 + 4x(+1) + one more +5 bonus = 20
        $this->assertSame(20, $user->xpTransactions()->sum('amount'));
    }

    public function test_current_streak_for_returns_zero_when_stale(): void
    {
        $user = User::factory()->create();

        DailyCheckin::factory()->create([
            'user_id' => $user->id,
            'date' => now()->subDays(5)->toDateString(),
            'streak_count' => 10,
        ]);

        $this->assertSame(0, app(CheckinService::class)->currentStreakFor($user));
    }
}
