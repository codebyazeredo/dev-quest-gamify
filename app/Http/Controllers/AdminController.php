<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __invoke(): View
    {
        Gate::authorize('accessAdminPanel', User::class);

        return view('admin.index');
    }
}
