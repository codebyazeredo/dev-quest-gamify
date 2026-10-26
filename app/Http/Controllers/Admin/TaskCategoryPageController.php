<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskCategory;
use Illuminate\Contracts\View\View;

class TaskCategoryPageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', TaskCategory::class);

        return view('admin.categories.index');
    }
}
