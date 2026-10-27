<?php

namespace App\Services;

use App\Enums\PullRequestState;
use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\Task;
use App\Repositories\BoardColumnRepository;
use App\Repositories\BoardRepository;
use App\Repositories\TaskRepository;

class GithubPullRequestService
{
    public function __construct(
        private BoardRepository $boards,
        private BoardColumnRepository $columns,
        private TaskRepository $tasks,
        private TaskService $taskService,
    ) {}

    public function sync(array $payload): string
    {
        $repositoryName = $payload['repository']['full_name'];
        $pullRequest = $payload['pull_request'];

        $board = $this->boards->findByGithubRepository($repositoryName);

        if ($board === null) {
            return "Nenhum quadro está vinculado ao repositório {$repositoryName}.";
        }

        $task = $this->tasks->findLinkedToPullRequest($board, $pullRequest['html_url'], $pullRequest['head']['ref']);

        if ($task === null) {
            return "Nenhuma tarefa do quadro \"{$board->name}\" está vinculada ao pull request {$pullRequest['html_url']}.";
        }

        $state = $this->stateOf($pullRequest);

        $this->tasks->update($task, [
            'pull_request_state' => $state,
            'pull_request_url' => $task->pull_request_url ?? $pullRequest['html_url'],
        ]);

        $this->advanceTask($task, $board, $state);

        return "Tarefa #{$task->id} atualizada: {$state->label()}.";
    }

    private function stateOf(array $pullRequest): PullRequestState
    {
        if ($pullRequest['merged'] ?? false) {
            return PullRequestState::MERGED;
        }

        return $pullRequest['state'] === 'open' ? PullRequestState::OPEN : PullRequestState::CLOSED;
    }

    private function advanceTask(Task $task, Board $board, PullRequestState $state): void
    {
        $targetStatus = match ($state) {
            PullRequestState::OPEN => TaskStatus::REVIEW,
            PullRequestState::MERGED => TaskStatus::TESTING,
            PullRequestState::CLOSED => null,
        };

        $canAdvance = $targetStatus !== null
            && $task->assigned_to !== null
            && $task->completed_at === null
            && $task->status->value < $targetStatus->value;

        if (! $canAdvance) {
            return;
        }

        $destination = $this->columns->findTaggedWith($board, $targetStatus);

        if ($destination === null) {
            return;
        }

        $this->taskService->move($task, $destination, $this->tasks->countInColumn($destination->id), $task->assignedTo);
    }
}
