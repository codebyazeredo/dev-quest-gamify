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

        $achievements = Achievement::where('active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Achievement $achievement) => [
                'achievement' => $achievement,
                'unlocked' => in_array($achievement->id, $unlockedIds, true),
                'progress' => $achievementService->valueFor($user, $achievement->condition_type),
            ]);

        return view('livewire.gamification.achievements', ['achievements' => $achievements]);
    }
}
