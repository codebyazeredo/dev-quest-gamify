<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AdminHubMenu;
use Illuminate\Contracts\View\View;

class AdminHubPageController extends Controller
{
    public function index(): View
    {
        $this->authorize('accessAdminPanel', User::class);

        return view('admin.hub', ['sections' => AdminHubMenu::sections()]);
    }
}
