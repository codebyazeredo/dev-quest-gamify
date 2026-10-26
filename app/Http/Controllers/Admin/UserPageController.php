<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class UserPageController extends Controller
{
    public function index(): View
    {
        $this->authorize('accessAdminPanel', User::class);

        return view('admin.users.index');
    }
}
