<?php

namespace App\Services;

use App\Enums\ChallengeType;
use App\Enums\XpSourceType;
use App\Events\ChallengeCompleted;
use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class ChallengeService
{
    public function __construct(private XpService $xpService) {}

    public function recordProgress(User $user, ChallengeType $type, CarbonInterface $occurredAt, int $amount = 1): void
    {
        Challenge::where('active', true)
            ->where('type', $type)
            ->where('starts_at', '<=', $occurredAt)
            ->where('ends_at', '>=', $occurredAt)
            ->get()
            ->each(fn (Challenge $challenge) => $this->incrementProgress($user, $challenge, $amount));
    }

    protected function incrementProgress(User $user, Challenge $challenge, int $amount): void
    {
        DB::transaction(function () use ($user, $challenge, $amount) {
            $userChallenge = UserChallenge::firstOrCreate(
                ['user_id' => $user->id, 'challenge_id' => $challenge->id],
                ['progress' => 0]
            );

            if ($userChallenge->completed_at !== null) {
                return;
            }

            $userChallenge->increment('progress', $amount);

            if ($userChallenge->progress >= $challenge->target) {
                $userChallenge->update(['completed_at' => now()]);

                if ($challenge->xp_reward > 0) {
                    $this->xpService->grant(
                        $user,
                        $challenge->xp_reward,
                        XpSourceType::CHALLENGE,
                        $challenge->id,
                        "Challenge completed - {$challenge->name}",
                    );
                }

                event(new ChallengeCompleted($user, $challenge));
            }
        });
    }
}
