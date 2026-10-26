<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskEventRule;
use Illuminate\Contracts\View\View;

class EventRulePageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', TaskEventRule::class);

        return view('admin.event-rules.index');
    }
}
