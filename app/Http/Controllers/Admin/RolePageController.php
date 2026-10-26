<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class RolePageController extends Controller
{
    public function index(): View
    {
        $this->authorize('accessAdminPanel', User::class);

        return view('admin.roles.index');
    }
}
