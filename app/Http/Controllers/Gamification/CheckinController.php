<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Services\CheckinService;
use App\Support\ToastCollector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function __construct(
        private CheckinService $checkins,
        private ToastCollector $toasts,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $alreadyCheckedInToday = $this->checkins->hasCheckedInToday($user);

        $this->checkins->checkIn($user);

        if (! $alreadyCheckedInToday) {
            $this->toasts->push('checkin', 'Check-in realizado!', 'Você ganhou XP só por aparecer hoje. Volte amanhã para manter a sequência.');
        }

        return response()->json(['data' => [
            'checked_in_today' => true,
            'current_streak' => $this->checkins->currentStreakFor($user),
        ]]);
    }
}
