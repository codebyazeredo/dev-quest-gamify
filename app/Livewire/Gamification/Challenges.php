<?php

namespace App\Livewire\Gamification;

use App\Models\Challenge;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Challenges extends Component
{
    public function render(): View
    {
        $user = auth()->user();

        $userChallenges = $user->userChallenges()->get()->keyBy('challenge_id');

        $challenges = Challenge::where('active', true)
            ->where('ends_at', '>=', now())
            ->orderBy('ends_at')
            ->get()
            ->map(function (Challenge $challenge) use ($userChallenges) {
                $userChallenge = $userChallenges->get($challenge->id);

                return [
                    'challenge' => $challenge,
                    'progress' => $userChallenge !== null ? $userChallenge->progress : 0,
                    'completed' => $userChallenge !== null && $userChallenge->completed_at !== null,
                ];
            });

        return view('livewire.gamification.challenges', ['challenges' => $challenges]);
    }
}
