<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ChallengeType;
use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Support\FlavorIcons;
use Illuminate\Contracts\View\View;

class ChallengePageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Challenge::class);

        return view('admin.challenges.index', [
            'challengeTypes' => ChallengeType::cases(),
            'icons' => FlavorIcons::all(),
        ]);
    }
}
