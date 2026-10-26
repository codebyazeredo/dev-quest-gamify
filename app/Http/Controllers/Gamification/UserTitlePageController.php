<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Services\TitleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserTitlePageController extends Controller
{
    public function __construct(private TitleService $titles) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('gamification.titles', [
            ...$this->titles->catalogFor($user),
            'selectedTitleId' => $user->selected_title_id,
        ]);
    }
}
