<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\UserDashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardPageController extends Controller
{
    public function __construct(private UserDashboardService $dashboard) {}

    public function index(Request $request): View
    {
        return view('dashboard.index', $this->dashboard->overviewFor($request->user()->load('selectedTitle')));
    }
}
