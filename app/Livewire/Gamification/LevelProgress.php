<?php

namespace App\Livewire\Gamification;

use App\Services\LevelService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class LevelProgress extends Component
{
    #[On('checked-in')]
    public function refresh(): void
    {
        // no-op: Livewire re-renders this component after any listener call
    }

    public function render(): View
    {
        $user = auth()->user();
        $levelService = app(LevelService::class);

        $totalXp = $levelService->totalXpFor($user);
        $currentLevel = $levelService->currentLevelFor($user);
        $nextLevel = $levelService->nextLevelFor($currentLevel);

        $xpIntoLevel = $totalXp - $currentLevel->xp_required;
        $xpForNext = $nextLevel ? $nextLevel->xp_required - $currentLevel->xp_required : 0;

        return view('livewire.gamification.level-progress', [
            'totalXp' => $totalXp,
            'currentLevel' => $currentLevel,
            'nextLevel' => $nextLevel,
            'xpIntoLevel' => $xpIntoLevel,
            'xpForNext' => $xpForNext,
            'participatesInLeveling' => $levelService->participatesInLeveling($user),
        ]);
    }
}
