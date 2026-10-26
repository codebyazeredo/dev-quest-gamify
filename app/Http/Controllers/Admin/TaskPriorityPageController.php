<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskPriority;
use Illuminate\Contracts\View\View;

class TaskPriorityPageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', TaskPriority::class);

        return view('admin.priorities.index');
    }
}
