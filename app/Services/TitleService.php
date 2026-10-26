<?php

namespace App\Services;

use App\Enums\TitleUnlockType;
use App\Events\TitleUnlocked;
use App\Models\Challenge;
use App\Models\Title;
use App\Models\User;
use App\Repositories\TitleRepository;
use App\Repositories\UserChallengeRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserTitleRepository;

class TitleService
{
    public function __construct(
        private TitleRepository $titles,
        private UserTitleRepository $userTitles,
        private UserChallengeRepository $userChallenges,
        private UserRepository $users,
    ) {}

    public function select(User $user, Title $title): void
    {
        $user->update(['selected_title_id' => $title->id]);
    }

    public function clear(User $user): void
    {
        $user->update(['selected_title_id' => null]);
    }

    public function grantForCompletedChallenge(User $user, Challenge $challenge): void
    {
        foreach ($this->titles->activeForChallenge($challenge->id) as $title) {
            $this->grant($user, $title);
        }

        $completedTotal = $this->userChallenges->countCompletedBy($user);

        foreach ($this->titles->activeByUnlockType(TitleUnlockType::CHALLENGES_COUNT) as $title) {
            if ($completedTotal >= (int) $title->challenges_required) {
                $this->grant($user, $title);
            }
        }
    }

    public function grantToEligibleUsers(Title $title): int
    {
        if (! $title->active) {
            return 0;
        }

        $eligibleUserIds = match ($title->unlock_type) {
            TitleUnlockType::CHALLENGE => $title->challenge
                ? $this->userChallenges->userIdsWhoCompleted($title->challenge)
                : [],
            TitleUnlockType::CHALLENGES_COUNT => $this->userChallenges->userIdsWithAtLeastCompleted(max(1, (int) $title->challenges_required)),
            default => [],
        };

        $granted = 0;

        foreach ($this->users->findMany($eligibleUserIds) as $user) {
            $granted += $this->grant($user, $title) ? 1 : 0;
        }

        return $granted;
    }

    private function grant(User $user, Title $title): bool
    {
        if (! $this->userTitles->grant($user->id, $title->id)) {
            return false;
        }

        event(new TitleUnlocked($user, $title));

        return true;
    }
}
