<?php

namespace Database\Seeders;

use App\Enums\PullRequestState;
use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskChecklistItem;
use App\Models\TaskComment;
use App\Models\TaskPriority;
use App\Models\User;
use App\Notifications\TaskProgress;
use App\Services\CheckinService;
use App\Services\TaskService;
use Database\Seeders\Data\TmsScenario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Histórico de trabalho da equipe: tarefas percorrendo o fluxo real (criação, desenvolvimento, revisão,
 * teste, aprovação e deploy) com data retroativa, o que gera XP, níveis e conquistas de forma coerente.
 */
class DemoDataSeeder extends Seeder
{
    private const USER_EMAILS = [
        'admin' => 'admin@devquestgamify.test',
        'po' => 'po@devquestgamify.test',
        'rafael' => 'rafael.monteiro@devquestgamify.test',
        'lucas' => 'dev@devquestgamify.test',
        'thiago' => 'thiago.barros@devquestgamify.test',
        'qa' => 'tester@devquestgamify.test',
        'sup1' => 'suporte@devquestgamify.test',
        'sup2' => 'diego.martins@devquestgamify.test',
    ];

    private const CHECKIN_STREAKS = ['rafael' => 16, 'lucas' => 7, 'thiago' => 3, 'qa' => 9, 'sup1' => 12, 'sup2' => 4, 'po' => 6, 'admin' => 2];

    private const DEV_HOURS_BY_PRIORITY = ['Baixa' => 8, 'Normal' => 18, 'Alta' => 30, 'Crítica' => 20];

    private TaskService $taskService;

    /** @var array<string, User> */
    private array $users = [];

    /** @var array<int, Collection<int, BoardColumn>> */
    private array $columns = [];

    /** @var array<string, Task> */
    private array $tasks = [];

    private Collection $categories;

    private Collection $priorities;

    private Carbon $realNow;

    private int $pullRequestNumber = 40;

    public function run(): void
    {
        $this->realNow = Carbon::now();
        $this->taskService = app(TaskService::class);

        $this->users = collect(self::USER_EMAILS)->map(fn (string $email) => User::where('email', $email)->firstOrFail())->all();
        $this->categories = TaskCategory::pluck('id', 'name');
        $this->priorities = TaskPriority::pluck('id', 'name');

        foreach (Board::orderBy('id')->get() as $index => $board) {
            $this->columns[$index] = BoardColumn::where('board_id', $board->id)->get()->keyBy(fn (BoardColumn $column) => $column->status->value);
        }

        $this->seedCheckins();

        foreach (TmsScenario::tasks() as $definition) {
            $this->tasks[$definition['key']] = $this->runTask($definition);
        }

        Carbon::setTestNow();

        $this->seedDiscussion();
        $this->seedSelectedTitles();
        $this->seedNotifications();
    }

    private function seedCheckins(): void
    {
        $checkins = app(CheckinService::class);

        foreach (self::CHECKIN_STREAKS as $userKey => $days) {
            Carbon::setTestNow($this->realNow->copy()->subDays($days));

            for ($i = 0; $i < $days; $i++) {
                $checkins->checkIn($this->users[$userKey]->fresh());
                Carbon::setTestNow(Carbon::now()->addDay());
            }
        }

        Carbon::setTestNow();
    }

    /** @param  array<string, mixed>  $definition */
    private function runTask(array $definition): Task
    {
        $columns = $this->columns[$definition['board']];
        $creator = $this->users[$definition['by']];
        $developer = $definition['dev'] !== null ? $this->users[$definition['dev']] : null;
        $tester = $this->users['qa'];
        $stage = $definition['stage'];

        Carbon::setTestNow($this->realNow->copy()->subDays($definition['start'])->setTime(9 + crc32($definition['key']) % 6, 15));

        $task = $this->taskService->create([
            'title' => $definition['title'],
            'description' => $definition['description'],
            'category_id' => $this->categories[$definition['category']],
            'priority_id' => $this->priorities[$definition['priority']],
            'column_id' => $columns[TaskStatus::BACKLOG->value]->id,
            'assigned_to' => $developer?->id,
            'due_at' => $this->dueAt($definition),
        ], $creator);

        if ($stage === 'backlog') {
            Carbon::setTestNow();

            return $task->refresh();
        }

        $this->advance(1);
        $this->taskService->move($task, $columns[TaskStatus::TODO->value], 0, $creator);

        if ($stage === 'todo') {
            return $this->finish($task, $definition);
        }

        $this->advance(3);
        $this->taskService->move($task, $columns[TaskStatus::DOING->value], 0, $developer);

        if ($stage === 'doing') {
            return $this->finish($task, $definition);
        }

        $this->advance(self::DEV_HOURS_BY_PRIORITY[$definition['priority']]);
        $this->taskService->move($task, $columns[TaskStatus::REVIEW->value], 0, $developer);

        if ($stage === 'review') {
            return $this->finish($task, $definition);
        }

        $this->advance(3);
        $this->taskService->move($task, $columns[TaskStatus::TESTING->value], 0, $tester);

        if ($stage === 'testing') {
            return $this->finish($task, $definition);
        }

        $this->advance(4);

        if ($stage === 'rejected') {
            $this->taskService->reject($task, $tester, $definition['reason']);

            return $this->finish($task, $definition);
        }

        $this->taskService->approve($task, $tester);

        if ($stage === 'approved') {
            return $this->finish($task, $definition);
        }

        $this->advance(1);
        $this->taskService->markHomologationCompleted($task, $developer);
        $this->advance(2);
        $this->taskService->markDeployed($task, $developer);

        if ($definition['start'] >= 44) {
            $this->taskService->archive($task);
        }

        return $this->finish($task, $definition);
    }

    /** @param  array<string, mixed>  $definition */
    private function finish(Task $task, array $definition): Task
    {
        Carbon::setTestNow();

        $task->refresh();

        if ($definition['dev'] !== null && in_array($definition['stage'], ['doing', 'review', 'testing', 'approved', 'rejected', 'done'], true)) {
            $this->attachBranch($task, $definition);
        }

        return $task;
    }

    /** @param  array<string, mixed>  $definition */
    private function attachBranch(Task $task, array $definition): void
    {
        $prefix = match (true) {
            $definition['category'] === 'Bug' && $definition['priority'] === 'Crítica' => 'hotfix',
            $definition['category'] === 'Bug' => 'bugfix',
            default => 'feature',
        };

        $attributes = ['branch_name' => "{$prefix}/dq-{$task->id}-".Str::limit(Str::slug($definition['title']), 36, '')];

        if (in_array($definition['stage'], ['review', 'testing', 'approved', 'rejected', 'done'], true)) {
            $repository = $definition['board'] === 0 ? 'rotamax/tms-core' : 'rotamax/tms-integracoes';
            $attributes['pull_request_url'] = "https://github.com/{$repository}/pull/".++$this->pullRequestNumber;
            $attributes['pull_request_state'] = in_array($definition['stage'], ['approved', 'done'], true) ? PullRequestState::MERGED : PullRequestState::OPEN;
        }

        $task->forceFill($attributes)->save();
    }

    /** @param  array<string, mixed>  $definition */
    private function dueAt(array $definition): ?string
    {
        if ($definition['stage'] === 'done') {
            return Carbon::now()->addDays(7)->format('Y-m-d H:i:s');
        }

        return isset($definition['due']) ? $this->realNow->copy()->addDays($definition['due'])->format('Y-m-d H:i:s') : null;
    }

    private function advance(int $hours): void
    {
        Carbon::setTestNow(Carbon::now()->addHours($hours));
    }

    private function seedDiscussion(): void
    {
        foreach (TmsScenario::comments() as [$key, $author, $hoursAfter, $body]) {
            $task = $this->tasks[$key];
            $comment = TaskComment::create(['task_id' => $task->id, 'user_id' => $this->users[$author]->id, 'body' => $body]);
            $comment->forceFill(['created_at' => $task->created_at->copy()->addHours($hoursAfter)])->save();
        }

        foreach (TmsScenario::checklists() as $key => $items) {
            $task = $this->tasks[$key];

            foreach ($items as [$title, $done]) {
                TaskChecklistItem::create([
                    'task_id' => $task->id,
                    'title' => $title,
                    'completed_at' => $done ? $task->created_at->copy()->addHours(12) : null,
                    'completed_by' => $done ? $task->assigned_to : null,
                ]);
            }
        }
    }

    private function seedSelectedTitles(): void
    {
        User::whereHas('unlockedTitles')->get()->each(function (User $user) {
            $title = $user->unlockedTitles()->with('title')->get()->pluck('title')->filter()->first();

            if ($title !== null) {
                $user->update(['selected_title_id' => $title->id]);
            }
        });
    }

    private function seedNotifications(): void
    {
        DB::table('notifications')->delete();

        $kinds = [
            ['assigned', 'atribuiu esta tarefa a você.'],
            ['commented', 'comentou na tarefa.'],
            ['testing', 'moveu para Em Teste, pronta para validação.'],
            ['approved', 'aprovou a tarefa.'],
            ['rejected', 'reprovou a tarefa: faltou cobrir o caso de borda.'],
            ['moved', 'moveu para Em Andamento.'],
        ];

        foreach (['admin', 'po', 'lucas', 'rafael', 'qa'] as $userKey) {
            $user = $this->users[$userKey];
            $tasks = Task::whereNull('archived_at')->where(fn ($query) => $query->where('assigned_to', $user->id)->orWhere('created_by', $user->id))->latest('id')->limit(6)->get();

            foreach ($tasks as $index => $task) {
                [$kind, $message] = $kinds[$index % count($kinds)];
                $actor = User::where('id', '!=', $user->id)->inRandomOrder()->first();

                $user->notify(new TaskProgress($task->id, $task->title, $kind, "{$actor->name} {$message}"));
            }

            $user->notifications()->get()->each(function ($notification, $index) {
                $notification->forceFill([
                    'created_at' => now()->subMinutes(($index + 1) * 37),
                    'read_at' => $index >= 3 ? now()->subMinutes($index * 20) : null,
                ])->save();
            });
        }
    }
}
