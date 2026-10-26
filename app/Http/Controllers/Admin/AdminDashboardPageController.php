<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\DashboardService;
use Illuminate\Contracts\View\View;

class AdminDashboardPageController extends Controller
{
    public function __construct(private DashboardService $dashboard) {}

    public function index(): View
    {
        $this->authorize('accessAdminPanel', User::class);

        return view('admin.dashboard', ['overview' => $this->dashboard->overview()]);
    }
}
