<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AchievementConditionType;
use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Support\FlavorIcons;
use Illuminate\Contracts\View\View;

class AchievementPageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Achievement::class);

        return view('admin.achievements.index', [
            'conditionTypes' => AchievementConditionType::cases(),
            'icons' => FlavorIcons::all(),
        ]);
    }
}
