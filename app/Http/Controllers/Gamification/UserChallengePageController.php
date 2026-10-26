<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Services\ChallengeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserChallengePageController extends Controller
{
    public function __construct(private ChallengeService $challenges) {}

    public function index(Request $request): View
    {
        return view('gamification.challenges', ['challenges' => $this->challenges->currentAndUpcomingFor($request->user())]);
    }
}
