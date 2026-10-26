<?php

namespace App\Services;

use App\Enums\ChallengeType;
use App\Enums\XpSourceType;
use App\Events\StreakBonusEarned;
use App\Models\DailyCheckin;
use App\Models\User;
use App\Repositories\DailyCheckinRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CheckinService
{
    public const DAILY_XP = 1;

    public const STREAK_BONUS_INTERVAL = 5;

    public const STREAK_BONUS_AMOUNT = 5;

    public function __construct(
        private XpService $xpService,
        private ChallengeService $challengeService,
        private AchievementService $achievementService,
        private DailyCheckinRepository $checkins,
    ) {}

    public function checkIn(User $user): DailyCheckin
    {
        return DB::transaction(function () use ($user) {
            $today = now()->toDateString();

            $existing = $this->checkins->findForDate($user, $today);

            if ($existing !== null) {
                return $existing;
            }

            $yesterday = now()->subDay()->toDateString();
            $previous = $this->checkins->findForDate($user, $yesterday);
            $streakCount = $previous !== null ? $previous->streak_count + 1 : 1;

            $checkin = $this->checkins->create([
                'user_id' => $user->id,
                'date' => $today,
                'streak_count' => $streakCount,
            ]);

            $this->xpService->grant($user, self::DAILY_XP, XpSourceType::CHECKIN, $checkin->id, 'Check-in diário');

            if ($streakCount % self::STREAK_BONUS_INTERVAL === 0) {
                $this->xpService->grant(
                    $user,
                    self::STREAK_BONUS_AMOUNT,
                    XpSourceType::CHECKIN,
                    $checkin->id,
                    "Bônus de sequência de {$streakCount} dias",
                );

                event(new StreakBonusEarned($user, $streakCount, self::STREAK_BONUS_AMOUNT));
            }

            $this->challengeService->recordProgress($user, ChallengeType::CHECKINS_MADE, now());
            $this->achievementService->evaluateForUser($user);

            return $checkin;
        });
    }

    public function hasCheckedInToday(User $user): bool
    {
        return $this->checkins->findForDate($user, now()->toDateString()) !== null;
    }

    public function currentStreakFor(User $user): int
    {
        $latest = $this->checkins->latestFor($user);

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

    /**
     * Dias exibidos no calendário do mês (semanas completas, de segunda a domingo).
     *
     * @return Collection<int, array{date: Carbon, inMonth: bool, isToday: bool, isFuture: bool, checkedIn: bool}>
     */
    public function calendarFor(User $user, Carbon $month): Collection
    {
        $monthStart = $month->copy()->startOfMonth();
        $gridStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $monthStart->copy()->endOfMonth()->endOfWeek(Carbon::MONDAY);

        $checkedInDates = $this->checkins->datesBetween($user, $gridStart->toDateString(), $gridEnd->toDateString());

        $days = collect();

        for ($cursor = $gridStart->copy(); $cursor->lte($gridEnd); $cursor->addDay()) {
            $days->push([
                'date' => $cursor->copy(),
                'inMonth' => $cursor->month === $monthStart->month,
                'isToday' => $cursor->isToday(),
                'isFuture' => $cursor->isFuture(),
                'checkedIn' => in_array($cursor->toDateString(), $checkedInDates, true),
            ]);
        }

        return $days;
    }
}
