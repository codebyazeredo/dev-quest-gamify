<?php

namespace App\Services;

use App\Enums\XpSourceType;
use App\Events\StreakBonusEarned;
use App\Models\DailyCheckin;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckinService
{
    public const DAILY_XP = 1;

    public const STREAK_BONUS_INTERVAL = 5;

    public const STREAK_BONUS_AMOUNT = 5;

    public function __construct(private XpService $xpService) {}

    public function checkIn(User $user): DailyCheckin
    {
        return DB::transaction(function () use ($user) {
            $today = now()->toDateString();

            $existing = DailyCheckin::where('user_id', $user->id)->where('date', $today)->first();

            if ($existing !== null) {
                return $existing;
            }

            $yesterday = now()->subDay()->toDateString();
            $previous = DailyCheckin::where('user_id', $user->id)->where('date', $yesterday)->first();
            $streakCount = $previous !== null ? $previous->streak_count + 1 : 1;

            $checkin = DailyCheckin::create([
                'user_id' => $user->id,
                'date' => $today,
                'streak_count' => $streakCount,
            ]);

            $this->xpService->grant($user, self::DAILY_XP, XpSourceType::CHECKIN, $checkin->id, 'Daily check-in');

            if ($streakCount % self::STREAK_BONUS_INTERVAL === 0) {
                $this->xpService->grant(
                    $user,
                    self::STREAK_BONUS_AMOUNT,
                    XpSourceType::CHECKIN,
                    $checkin->id,
                    "{$streakCount}-day streak bonus",
                );

                event(new StreakBonusEarned($user, $streakCount, self::STREAK_BONUS_AMOUNT));
            }

            return $checkin;
        });
    }

    public function currentStreakFor(User $user): int
    {
        $latest = DailyCheckin::where('user_id', $user->id)->orderByDesc('date')->first();

        if ($latest === null) {
            return 0;
        }

        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        if (! in_array($latest->date->toDateString(), [$today, $yesterday], true)) {
            return 0;
        }

        return $latest->streak_count;
    }
}
