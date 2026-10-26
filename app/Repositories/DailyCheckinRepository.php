<?php

namespace App\Repositories;

use App\Models\DailyCheckin;
use App\Models\User;

class DailyCheckinRepository extends Repository
{
    protected function model(): string
    {
        return DailyCheckin::class;
    }

    public function findForDate(User $user, string $date): ?DailyCheckin
    {
        return $this->query()->where('user_id', $user->id)->where('date', $date)->first();
    }

    public function latestFor(User $user): ?DailyCheckin
    {
        return $this->query()->where('user_id', $user->id)->orderByDesc('date')->first();
    }

    public function countFor(User $user): int
    {
        return $this->query()->where('user_id', $user->id)->count();
    }

    /** @return array<int, string> datas (Y-m-d) com check-in dentro do intervalo, inclusive */
    public function datesBetween(User $user, string $start, string $end): array
    {
        return $this->query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->pluck('date')
            ->map(fn ($date) => $date->toDateString())
            ->all();
    }
}
