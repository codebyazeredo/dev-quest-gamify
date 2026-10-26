<?php

namespace App\Http\Controllers\Board;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Board;
use App\Repositories\TaskCategoryRepository;
use App\Repositories\TaskPriorityRepository;
use App\Services\BoardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BoardPageController extends Controller
{
    public function __construct(
        private BoardService $boards,
        private TaskCategoryRepository $categories,
        private TaskPriorityRepository $priorities,
    ) {}

    public function index(Request $request): View
    {
        return view('boards.index', ['boards' => $this->boards->listFor($request->user())]);
    }

    public function show(Request $request, Board $board): View
    {
        $this->authorize('view', $board);

        $kanban = $this->boards->kanban($board);
        $tasks = $kanban->columns->flatMap->tasks;

        return view('boards.show', [
            'board' => $kanban,
            'statuses' => TaskStatus::cases(),
            'categories' => $this->categories->all(),
            'priorities' => $this->priorities->all(),
            'assignees' => $tasks->pluck('assignedTo')->filter()->unique('id')->sortBy('name'),
            'taskForms' => $tasks->mapWithKeys(fn ($task) => [$task->id => TaskResource::make($task)->resolve()]),
            'assignableUsers' => $this->boards->assignableUsersFor($request->user()),
        ]);
    }

    public function archive(Board $board): View
    {
        $this->authorize('view', $board);

        return view('boards.archive', ['board' => $board, ...$this->boards->archivedTasks($board)]);
    }
}
