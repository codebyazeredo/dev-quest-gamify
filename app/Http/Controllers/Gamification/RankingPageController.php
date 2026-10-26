<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gamification\RankingRequest;
use App\Services\RankingService;
use Illuminate\Contracts\View\View;

class RankingPageController extends Controller
{
    public function __construct(private RankingService $ranking) {}

    public function index(RankingRequest $request): View
    {
        return view('gamification.ranking', [
            'users' => $this->ranking->paginate($request->role(), $request->period(), $request->perPage())->withQueryString(),
            'activeRole' => $request->role(),
            'period' => $request->period(),
        ]);
    }
}
