<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\TaskCategoryRepository;
use App\Repositories\TaskPriorityRepository;
use App\Services\TaskDetailService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TaskPageController extends Controller
{
    public function __construct(
        private TaskDetailService $details,
        private TaskCategoryRepository $categories,
        private TaskPriorityRepository $priorities,
    ) {}

    public function show(Request $request, Task $task): View
    {
        $this->authorize('view', $task);

        return view('tasks.show', [
            ...$this->details->detailsFor($task, $request->user()),
            'categories' => $this->categories->all(),
            'priorities' => $this->priorities->all(),
            'taskForms' => [$task->id => TaskResource::make($task)->resolve()],
        ]);
    }
}
