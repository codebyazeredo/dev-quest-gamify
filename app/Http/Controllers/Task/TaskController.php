<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AssignTaskRequest;
use App\Http\Requests\Task\MoveTaskRequest;
use App\Http\Requests\Task\RejectTaskRequest;
use App\Http\Requests\Task\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\BoardColumnService;
use App\Services\TaskService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $tasks,
        private BoardColumnService $columns,
    ) {}

    public function store(TaskRequest $request): JsonResponse
    {
        $this->authorize('create', Task::class);

        $user = $request->user();
        $data = $request->validated();

        if (! ($user->isAdmin() || $user->isProductOwner())) {
            $data['assigned_to'] = null;
        }

        $task = $this->tasks->create($data, $user);

        return TaskResource::make($task)
            ->additional(['message' => "Tarefa \"{$task->title}\" criada."])
            ->response()
            ->setStatusCode(201);
    }

    public function update(TaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task = $this->tasks->updateDetails($task, $request->validated());

        return TaskResource::make($task)
            ->additional(['message' => "Tarefa \"{$task->title}\" atualizada."])
            ->response();
    }

    public function move(MoveTaskRequest $request, Task $task): JsonResponse
    {
        $destination = $this->columns->findInBoard($task->board, $request->validated('column_id'));
        $user = $request->user();

        if (Gate::forUser($user)->denies('move', [$task, $destination])) {
            throw new AuthorizationException($this->tasks->explainMoveDenial($task, $destination, $user));
        }

        $task = $this->tasks->move($task, $destination, $request->validated('position'), $user);

        return TaskResource::make($task)
            ->additional(['message' => "Tarefa \"{$task->title}\" movida para {$destination->name}."])
            ->response();
    }

    public function claim(Request $request, Task $task): JsonResponse
    {
        $this->authorize('claim', $task);

        $task = $this->tasks->assign($task, $request->user());

        return TaskResource::make($task)
            ->additional(['message' => 'Você agora é o responsável por esta tarefa.'])
            ->response();
    }

    public function assign(AssignTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('assignAny', Task::class);

        $task = $this->tasks->assignToUser($task, $request->validated('user_id'), $request->user());

        return TaskResource::make($task)
            ->additional(['message' => 'A tarefa foi atribuída com sucesso.'])
            ->response();
    }

    public function approve(Request $request, Task $task): JsonResponse
    {
        $this->authorize('approve', $task);

        $task = $this->tasks->approve($task, $request->user());

        return TaskResource::make($task)
            ->additional(['message' => 'Tarefa aprovada. Ela avançou para a próxima etapa.'])
            ->response();
    }

    public function reject(RejectTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('reject', $task);

        $task = $this->tasks->reject($task, $request->user(), $request->validated('reason'));

        return TaskResource::make($task)
            ->additional(['message' => 'Tarefa reprovada. O responsável foi notificado do motivo.'])
            ->response();
    }

    public function markHomologationCompleted(Request $request, Task $task): JsonResponse
    {
        $this->authorize('markHomologationCompleted', $task);

        $task = $this->tasks->markHomologationCompleted($task, $request->user());

        return TaskResource::make($task)
            ->additional(['message' => 'Homologação concluída. A tarefa foi movida para Concluído.'])
            ->response();
    }

    public function markDeployed(Request $request, Task $task): JsonResponse
    {
        $this->authorize('markDeployed', $task);

        $task = $this->tasks->markDeployed($task, $request->user());

        return TaskResource::make($task)
            ->additional(['message' => 'Implantação registrada. A tarefa foi marcada como implantada.'])
            ->response();
    }

    public function archive(Task $task): JsonResponse
    {
        $this->authorize('archive', $task);

        $task = $this->tasks->archive($task);

        return TaskResource::make($task)
            ->additional(['message' => 'Tarefa arquivada. Ela continua acessível pelo arquivo do quadro.'])
            ->response();
    }

    public function unarchive(Task $task): JsonResponse
    {
        $this->authorize('unarchive', $task);

        $task = $this->tasks->unarchive($task);

        return TaskResource::make($task)
            ->additional(['message' => 'Tarefa desarquivada. Ela voltou para o quadro.'])
            ->response();
    }
}
