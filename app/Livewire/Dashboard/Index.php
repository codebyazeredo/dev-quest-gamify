<?php

namespace App\Livewire\Dashboard;

use App\Models\Achievement;
use App\Models\Task;
use App\Models\Title;
use App\Models\XpTransaction;
use App\Services\CheckinService;
use App\Services\LevelService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public bool $showEditProfile = false;

    public function toggleEditProfile(): void
    {
        $this->showEditProfile = ! $this->showEditProfile;
    }

    #[On('checked-in')]
    public function refreshStats(): void {}

    #[On('close-modal')]
    #[On('profile-saved')]
    public function closeModal(): void
    {
        $this->showEditProfile = false;
    }

    public function render(): View
    {
        $user = auth()->user()->load('selectedTitle');
        $levelService = app(LevelService::class);

        $totalXp = $levelService->totalXpFor($user);
        $currentLevel = $levelService->currentLevelFor($user);

        $tasksCompleted = Task::where('assigned_to', $user->id)
            ->whereNotNull('completed_at')
            ->count();

        $xpThisWeek = (int) XpTransaction::where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfWeek())
            ->sum('amount');

        $usersAboveMe = DB::table('xp_transactions')
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('SUM(amount) > ?', [$totalXp])
            ->count();

        return view('livewire.dashboard.index', [
            'totalXp' => $totalXp,
            'currentLevel' => $currentLevel,
            'tasksCompleted' => $tasksCompleted,
            'xpThisWeek' => $xpThisWeek,
            'rankingPosition' => $usersAboveMe + 1,
            'achievementsCount' => $user->isAdmin() ? Achievement::where('active', true)->count() : $user->unlockedAchievements()->count(),
            'titlesCount' => $user->isAdmin() ? Title::count() : $user->unlockedTitles()->count(),
            'currentStreak' => app(CheckinService::class)->currentStreakFor($user),
            'selectedTitle' => $user->selectedTitle,
        ]);
    }
}
