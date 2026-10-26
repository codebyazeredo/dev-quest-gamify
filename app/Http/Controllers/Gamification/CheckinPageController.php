<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gamification\CheckinHistoryRequest;
use App\Services\CheckinService;
use Illuminate\Contracts\View\View;

class CheckinPageController extends Controller
{
    public function __construct(private CheckinService $checkins) {}

    public function index(CheckinHistoryRequest $request): View
    {
        $user = $request->user();
        $month = $request->month();

        return view('checkin.history', [
            'days' => $this->checkins->calendarFor($user, $month),
            'monthLabel' => ucfirst($month->locale('pt_BR')->translatedFormat('F \d\e Y')),
            'previousMonth' => $month->copy()->subMonthNoOverflow()->format('Y-m'),
            'nextMonth' => $month->copy()->addMonthNoOverflow()->format('Y-m'),
            'currentStreak' => $this->checkins->currentStreakFor($user),
            'checkedInToday' => $this->checkins->hasCheckedInToday($user),
        ]);
    }
}
