<?php

namespace App\Livewire\Gamification;

use App\Models\Achievement;
use App\Services\AchievementService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Achievements extends Component
{
    public function render(): View
    {
        $user = auth()->user();
        $achievementService = app(AchievementService::class);

        $unlockedIds = $user->unlockedAchievements()->pluck('achievement_id')->all();
        $cache = [];

        $isAdmin = $user->isAdmin();

        $achievements = Achievement::where('active', true)
            ->orderBy('name')
            ->get()
            ->map(function (Achievement $achievement) use ($user, $unlockedIds, $achievementService, $isAdmin, &$cache) {
                $cache[$achievement->condition_type->value] ??= $achievementService->valueFor($user, $achievement->condition_type);

                return [
                    'achievement' => $achievement,
                    'unlocked' => $isAdmin || in_array($achievement->id, $unlockedIds, true),
                    'progress' => $cache[$achievement->condition_type->value],
                ];
            });

        return view('livewire.gamification.achievements', ['achievements' => $achievements]);
    }
}
