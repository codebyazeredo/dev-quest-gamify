<?php

namespace App\Services;

use App\Models\Level;
use App\Models\User;
use App\Repositories\LevelRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class AssignableUserService
{
    private const PODIUM_SIZE = 3;

    public function __construct(
        private UserRepository $users,
        private LevelRepository $levels,
        private LevelService $levelService,
    ) {}

    /** Usuários atribuíveis com os atributos `level` e `podium_rank` (1 a 3, apenas desenvolvedores) para exibição. */
    public function all(): Collection
    {
        $users = $this->users->allForAssignment();
        $levels = $this->levels->all();
        $podium = $this->podiumPositionsOf($users);

        return $users->each(function (User $user) use ($levels, $podium) {
            $totalXp = max(0, (int) $user->total_xp);

            $level = $this->levelService->participatesInLeveling($user)
                ? $levels->where('xp_required', '<=', $totalXp)->max('level')
                : null;

            $user->setAttribute('level', $level);
            $user->setAttribute('level_tier', $level !== null ? Level::tierOf($level) : null);
            $user->setAttribute('podium_rank', $podium[$user->id] ?? null);
        });
    }

    /** @return array<int, int> posição (1 a 3) indexada pelo id do usuário */
    private function podiumPositionsOf(Collection $users): array
    {
        $topDevelopers = $users
            ->filter(fn (User $user) => $user->isDeveloper() && $user->total_xp > 0)
            ->sortByDesc('total_xp')
            ->take(self::PODIUM_SIZE)
            ->values();

        return $topDevelopers->mapWithKeys(fn (User $user, int $index) => [$user->id => $index + 1])->all();
    }
}
