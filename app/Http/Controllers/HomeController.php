<?php

namespace App\Http\Controllers;

use App\Services\BoardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(private BoardService $boards) {}

    public function index(Request $request): RedirectResponse
    {
        return $request->user()
            ? redirect($this->boards->landingUrl())
            : redirect()->route('login');
    }
}
