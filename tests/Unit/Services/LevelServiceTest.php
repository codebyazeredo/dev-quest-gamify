<?php

namespace Tests\Unit\Services;

use App\Models\Level;
use App\Models\User;
use App\Models\XpTransaction;
use App\Services\LevelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LevelServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_xp_for_sums_the_users_transactions(): void
    {
        $user = User::factory()->create();
        XpTransaction::factory()->create(['user_id' => $user->id, 'amount' => 10]);
        XpTransaction::factory()->create(['user_id' => $user->id, 'amount' => 20]);
        XpTransaction::factory()->create(['user_id' => User::factory()->create()->id, 'amount' => 999]);

        $this->assertSame(30, app(LevelService::class)->totalXpFor($user));
    }

    public function test_level_for_total_xp_returns_the_highest_level_at_or_below_total(): void
    {
        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
        Level::factory()->create(['level' => 2, 'xp_required' => 100]);
        Level::factory()->create(['level' => 3, 'xp_required' => 500]);

        $service = app(LevelService::class);

        $this->assertSame(1, $service->levelForTotalXp(0)->level);
        $this->assertSame(1, $service->levelForTotalXp(99)->level);
        $this->assertSame(2, $service->levelForTotalXp(100)->level);
        $this->assertSame(2, $service->levelForTotalXp(499)->level);
        $this->assertSame(3, $service->levelForTotalXp(500)->level);
    }

    public function test_level_for_total_xp_floors_at_level_one_for_zero_or_negative_total(): void
    {
        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
        Level::factory()->create(['level' => 2, 'xp_required' => 100]);

        $service = app(LevelService::class);

        $this->assertSame(1, $service->levelForTotalXp(0)->level);
        $this->assertSame(1, $service->levelForTotalXp(-50)->level);
    }

    public function test_next_level_for_returns_null_at_the_highest_level(): void
    {
        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
        $level2 = Level::factory()->create(['level' => 2, 'xp_required' => 100]);
        $level3 = Level::factory()->create(['level' => 3, 'xp_required' => 500]);

        $service = app(LevelService::class);

        $this->assertSame(3, $service->nextLevelFor($level2)->level);
        $this->assertNull($service->nextLevelFor($level3));
    }

    public function test_current_level_for_returns_max_level_for_admins_regardless_of_real_xp(): void
    {
        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
        Level::factory()->create(['level' => 2, 'xp_required' => 100]);
        $maxLevel = Level::factory()->create(['level' => 3, 'xp_required' => 500]);

        $admin = User::factory()->admin()->create();
        XpTransaction::factory()->create(['user_id' => $admin->id, 'amount' => 1]);

        $service = app(LevelService::class);

        $this->assertSame($maxLevel->level, $service->currentLevelFor($admin)->level);
        $this->assertNull($service->nextLevelFor($service->currentLevelFor($admin)));
        // the real XP total is untouched — only the displayed level is short-circuited
        $this->assertSame(1, $service->totalXpFor($admin));
    }

    public function test_current_level_for_a_non_admin_still_reflects_real_xp(): void
    {
        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
        Level::factory()->create(['level' => 2, 'xp_required' => 100]);

        $developer = User::factory()->developer()->create();

        $this->assertSame(1, app(LevelService::class)->currentLevelFor($developer)->level);
    }
}
