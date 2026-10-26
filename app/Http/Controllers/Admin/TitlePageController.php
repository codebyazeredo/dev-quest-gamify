<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TitleUnlockType;
use App\Http\Controllers\Controller;
use App\Models\Title;
use App\Support\FlavorIcons;
use Illuminate\Contracts\View\View;

class TitlePageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Title::class);

        return view('admin.titles.index', [
            'unlockTypes' => TitleUnlockType::cases(),
            'icons' => FlavorIcons::all(),
        ]);
    }
}
