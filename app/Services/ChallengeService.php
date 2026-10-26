<?php

namespace App\Services;

use App\Enums\ChallengeType;
use App\Enums\XpSourceType;
use App\Events\ChallengeCompleted;
use App\Models\Challenge;
use App\Models\User;
use App\Repositories\ChallengeRepository;
use App\Repositories\UserChallengeRepository;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChallengeService
{
    public function __construct(
        private XpService $xpService,
        private ChallengeRepository $challenges,
        private UserChallengeRepository $userChallenges,
    ) {}

    /**
     * Desafios em andamento ou agendados com o progresso do usuário em cada um.
     *
     * @return Collection<int, array{challenge: Challenge, progress: int, completed: bool}>
     */
    public function currentAndUpcomingFor(User $user): Collection
    {
        $progressByChallenge = $this->userChallenges->keyedByChallengeFor($user);

        return $this->challenges->activeCurrentAndUpcoming()->map(function (Challenge $challenge) use ($progressByChallenge) {
            $userChallenge = $progressByChallenge->get($challenge->id);

            return [
                'challenge' => $challenge,
                'progress' => $userChallenge !== null ? $userChallenge->progress : 0,
                'completed' => $userChallenge !== null && $userChallenge->completed_at !== null,
            ];
        });
    }

    public function recordProgress(User $user, ChallengeType $type, CarbonInterface $occurredAt, int $amount = 1): void
    {
        $this->challenges->activeOfTypeAt($type, $occurredAt)
            ->each(fn (Challenge $challenge) => $this->incrementProgress($user, $challenge, $amount));
    }

    protected function incrementProgress(User $user, Challenge $challenge, int $amount): void
    {
        DB::transaction(function () use ($user, $challenge, $amount) {
            $userChallenge = $this->userChallenges->firstOrCreateFor($user, $challenge);

            if ($userChallenge->completed_at !== null) {
                return;
            }

            $this->userChallenges->incrementProgress($userChallenge, $amount);

            if ($userChallenge->progress >= $challenge->target) {
                $this->userChallenges->markCompleted($userChallenge);

                if ($challenge->xp_reward > 0) {
                    $this->xpService->grant(
                        $user,
                        $challenge->xp_reward,
                        XpSourceType::CHALLENGE,
                        $challenge->id,
                        "Desafio concluído - {$challenge->name}",
                    );
                }

                event(new ChallengeCompleted($user, $challenge));
            }
        });
    }
}
