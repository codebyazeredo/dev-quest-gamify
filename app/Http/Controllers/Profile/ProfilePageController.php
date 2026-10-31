<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ProfilePageController extends Controller
{
    public function show(): View
    {
        return view('profile.show');
    }
}
