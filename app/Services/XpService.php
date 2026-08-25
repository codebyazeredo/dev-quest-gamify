<?php

namespace App\Services;

use App\Enums\XpSourceType;
use App\Events\LevelUp;
use App\Models\User;
use App\Models\XpTransaction;
use Illuminate\Support\Facades\DB;

class XpService
{
    public function __construct(private LevelService $levelService) {}

    public function grant(
        User $user,
        int $amount,
        XpSourceType $sourceType,
        ?int $sourceId,
        string $description,
    ): ?XpTransaction {
        if ($amount === 0) {
            return null;
        }

        return DB::transaction(function () use ($user, $amount, $sourceType, $sourceId, $description) {
            $totalBefore = $this->levelService->totalXpFor($user);
            $levelBefore = $this->levelService->levelForTotalXp($totalBefore);

            $transaction = XpTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'description' => $description,
            ]);

            $levelAfter = $this->levelService->levelForTotalXp($totalBefore + $amount);

            if ($levelAfter->level > $levelBefore->level) {
                event(new LevelUp($user, $levelBefore, $levelAfter));
            }

            return $transaction;
        });
    }
}
