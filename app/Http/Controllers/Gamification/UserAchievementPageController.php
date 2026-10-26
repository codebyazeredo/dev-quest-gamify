<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Services\AchievementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserAchievementPageController extends Controller
{
    public function __construct(private AchievementService $achievements) {}

    public function index(Request $request): View
    {
        return view('gamification.achievements', ['achievements' => $this->achievements->catalogFor($request->user())]);
    }
}
