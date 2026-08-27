<?php

namespace Database\Seeders;

use App\Enums\TaskStatus;
use App\Models\Address;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Person;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskPriority;
use App\Models\User;
use App\Services\CheckinService;
use App\Services\TaskService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Populates the dev database with a realistic day-to-day snapshot: extra
 * people/users across every role, a busy board with tasks spread across
 * every column (some overdue, some completed with a backdated history via
 * the real TaskService/CheckinService flow so XP/achievements/challenges
 * come out fully consistent), and a few days of check-in streaks.
 */
class DemoDataSeeder extends Seeder
{
    private TaskService $taskService;

    private CheckinService $checkinService;

    public function run(): void
    {
        $this->taskService = app(TaskService::class);
        $this->checkinService = app(CheckinService::class);

        $users = $this->seedPeopleAndUsers();
        $this->seedCheckins($users);
        $this->seedTasks($users);

        Carbon::setTestNow();
    }

    /**
     * @return array<string, array<int, User>>
     */
    private function seedPeopleAndUsers(): array
    {
        $accounts = [
            ['nome' => 'Marina Souza Ferreira', 'email' => 'marina.ferreira@devquestgamify.test', 'role' => 'dev', 'cpfBase' => '101202303', 'sexo' => 2],
            ['nome' => 'Rafael Oliveira Lima', 'email' => 'rafael.lima@devquestgamify.test', 'role' => 'dev', 'cpfBase' => '202303404', 'sexo' => 1],
            ['nome' => 'Camila Santos Rocha', 'email' => 'camila.rocha@devquestgamify.test', 'role' => 'dev', 'cpfBase' => '303404505', 'sexo' => 2],
            ['nome' => 'Bruno Costa Almeida', 'email' => 'bruno.almeida@devquestgamify.test', 'role' => 'tester', 'cpfBase' => '404505606', 'sexo' => 1],
            ['nome' => 'Juliana Pereira Martins', 'email' => 'juliana.martins@devquestgamify.test', 'role' => 'tester', 'cpfBase' => '505606707', 'sexo' => 2],
            ['nome' => 'Diego Fernandes Cardoso', 'email' => 'diego.cardoso@devquestgamify.test', 'role' => 'suporte', 'cpfBase' => '606707808', 'sexo' => 1],
            ['nome' => 'Larissa Gomes Barbosa', 'email' => 'larissa.barbosa@devquestgamify.test', 'role' => 'suporte', 'cpfBase' => '707808909', 'sexo' => 2],
            ['nome' => 'Thiago Ribeiro Nunes', 'email' => 'thiago.nunes@devquestgamify.test', 'role' => 'product_owner', 'cpfBase' => '808909010', 'sexo' => 1],
        ];

        $cities = [
            ['cidade' => 'São Paulo', 'estado' => 'SP', 'cep' => '01310-100', 'logradouro' => 'Avenida Paulista'],
            ['cidade' => 'Belo Horizonte', 'estado' => 'MG', 'cep' => '30130-010', 'logradouro' => 'Rua da Bahia'],
            ['cidade' => 'Curitiba', 'estado' => 'PR', 'cep' => '80010-000', 'logradouro' => 'Rua XV de Novembro'],
            ['cidade' => 'Porto Alegre', 'estado' => 'RS', 'cep' => '90010-150', 'logradouro' => 'Avenida Borges de Medeiros'],
            ['cidade' => 'Recife', 'estado' => 'PE', 'cep' => '50030-230', 'logradouro' => 'Avenida Conde da Boa Vista'],
        ];

        $usersByRole = [];

        foreach ($accounts as $i => $account) {
            $city = $cities[$i % count($cities)];

            $person = Person::firstOrCreate(
                ['email' => $account['email']],
                [
                    'nome' => $account['nome'],
                    'cpf' => $this->validCpf($account['cpfBase']),
                    'rg' => null,
                    'nascimento' => now()->subYears(rand(23, 42))->subDays(rand(0, 365))->format('Y-m-d'),
                    'sexo' => $account['sexo'],
                    'telefone1' => '119'.rand(10000000, 99999999),
                    'telefone2' => null,
                    'foto_path' => null,
                ]
            );

            Address::firstOrCreate(
                ['person_id' => $person->id],
                [
                    'cep' => $city['cep'],
                    'logradouro' => $city['logradouro'],
                    'numero' => (string) rand(50, 2500),
                    'cidade' => $city['cidade'],
                    'estado' => $city['estado'],
                ]
            );

            $user = User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['nome'],
                    'password' => 'password',
                    'email_verified_at' => now(),
                    'person_id' => $person->id,
                ]
            );

            $user->syncRoles([$account['role']]);

            $usersByRole[$account['role']][] = $user;
        }

        // fold in the original seeded accounts so the demo pool is bigger
        $usersByRole['dev'][] = User::where('email', 'dev@devquestgamify.test')->first();
        $usersByRole['tester'][] = User::where('email', 'tester@devquestgamify.test')->first();
        $usersByRole['suporte'][] = User::where('email', 'suporte@devquestgamify.test')->first();
        $usersByRole['product_owner'][] = User::where('email', 'po@devquestgamify.test')->first();

        return $usersByRole;
    }

    /**
     * @param  array<string, array<int, User>>  $users
     */
    private function seedCheckins(array $users): void
    {
        $streaks = [
            [$users['dev'][0] ?? null, 12],
            [$users['dev'][1] ?? null, 6],
            [$users['tester'][0] ?? null, 4],
            [$users['suporte'][0] ?? null, 8],
            [$users['dev'][2] ?? null, 1],
        ];

        foreach ($streaks as [$user, $days]) {
            if (! $user instanceof User) {
                continue;
            }

            Carbon::setTestNow(now()->subDays($days));

            for ($i = 0; $i < $days; $i++) {
                $this->checkinService->checkIn($user->fresh());
                Carbon::setTestNow(now()->addDay());
            }

            Carbon::setTestNow();
        }
    }

    /**
     * @param  array<string, array<int, User>>  $users
     */
    private function seedTasks(array $users): void
    {
        $board = Board::first();
        $columns = BoardColumn::where('board_id', $board->id)->get()->keyBy(fn ($c) => $c->status->value);
        $categories = TaskCategory::pluck('id', 'name');
        $priorities = TaskPriority::orderBy('multiplier')->get();

        $devs = $users['dev'];
        $testers = $users['tester'];
        $suporte = $users['suporte'];
        $creators = [...$suporte, $users['product_owner'][0]];

        $pool = $this->titlePool();

        // 1) A healthy batch of fully completed tasks, backdated over the
        // last few weeks, driven through the real approve/homologate flow so
        // XP, achievements and challenges come out consistent.
        $completedCount = 14;

        for ($i = 0; $i < $completedCount; $i++) {
            $category = array_rand($pool);
            [$title, $description] = $this->pickTitle($pool, $category);
            $dev = $devs[array_rand($devs)];
            $tester = $testers[array_rand($testers)];
            $creator = $creators[array_rand($creators)];
            $priority = $priorities[array_rand($priorities->all())];

            $daysAgo = rand(2, 21);
            Carbon::setTestNow(now()->subDays($daysAgo + 4));

            $task = $this->taskService->create([
                'title' => $title,
                'description' => $description,
                'category_id' => $categories[$category],
                'priority_id' => $priority->id,
                'column_id' => $columns[TaskStatus::BACKLOG->value]->id,
                'assigned_to' => $dev->id,
                'due_at' => now()->addDays(rand(3, 6))->format('Y-m-d H:i:s'),
            ], $creator);

            $this->taskService->move($task, $columns[TaskStatus::TODO->value], 0, $creator);
            $this->taskService->move($task, $columns[TaskStatus::DOING->value], 0, $dev);
            Carbon::setTestNow(now()->addDay());
            $this->taskService->move($task, $columns[TaskStatus::REVIEW->value], 0, $dev);
            Carbon::setTestNow(now()->addDay());
            $this->taskService->move($task, $columns[TaskStatus::TESTING->value], 0, $tester);
            Carbon::setTestNow(now()->addHours(4));
            $this->taskService->approve($task, $tester);
            Carbon::setTestNow(now()->addHours(3));
            $this->taskService->markHomologationCompleted($task, $dev);
            Carbon::setTestNow(now()->addHours(1));
            $this->taskService->markDeployed($task, $dev);

            Carbon::setTestNow();
        }

        // 2) Tasks spread across every other stage, with a realistic mix of
        // overdue / due-soon / due-later deadlines.
        $stages = [
            TaskStatus::BACKLOG->value => 9,
            TaskStatus::TODO->value => 6,
            TaskStatus::DOING->value => 5,
            TaskStatus::REVIEW->value => 3,
            TaskStatus::TESTING->value => 3,
            TaskStatus::APPROVED->value => 2,
        ];

        $dueOffsets = [-6, -3, -1, 1, 2, 4, 7, 14, 21, null, null];

        foreach ($stages as $statusValue => $count) {
            for ($i = 0; $i < $count; $i++) {
                $category = array_rand($pool);
                [$title, $description] = $this->pickTitle($pool, $category);
                $priority = $priorities[array_rand($priorities->all())];
                $creator = $creators[array_rand($creators)];

                $assignTo = $statusValue === TaskStatus::BACKLOG->value && rand(0, 1) === 0
                    ? null
                    : $devs[array_rand($devs)]->id;

                $offset = $dueOffsets[array_rand($dueOffsets)];
                $dueAt = $offset === null ? null : now()->addDays($offset)->format('Y-m-d H:i:s');

                $task = $this->taskService->create([
                    'title' => $title,
                    'description' => $description,
                    'category_id' => $categories[$category],
                    'priority_id' => $priority->id,
                    'column_id' => $columns[TaskStatus::BACKLOG->value]->id,
                    'assigned_to' => $assignTo,
                    'due_at' => $dueAt,
                ], $creator);

                if ($statusValue === TaskStatus::BACKLOG->value) {
                    continue;
                }

                $dev = $assignTo !== null ? User::find($assignTo) : $devs[array_rand($devs)];
                $tester = $testers[array_rand($testers)];

                $this->taskService->move($task, $columns[TaskStatus::TODO->value], 0, $creator);

                if ($statusValue >= TaskStatus::DOING->value) {
                    $this->taskService->move($task, $columns[TaskStatus::DOING->value], 0, $dev);
                }
                if ($statusValue >= TaskStatus::REVIEW->value) {
                    $this->taskService->move($task, $columns[TaskStatus::REVIEW->value], 0, $dev);
                }
                if ($statusValue >= TaskStatus::TESTING->value) {
                    $this->taskService->move($task, $columns[TaskStatus::TESTING->value], 0, $tester);
                }
                if ($statusValue >= TaskStatus::APPROVED->value) {
                    $this->taskService->approve($task, $tester);
                }
            }
        }
    }

    /**
     * @return array<string, array<int, array{0: string, 1: string}>>
     */
    private function titlePool(): array
    {
        return [
            'Bug' => [
                ['Corrigir erro de login intermitente', 'Alguns usuários relatam falha ao autenticar após período de inatividade da sessão.'],
                ['Resolver vazamento de memória no dashboard', 'O consumo de memória do navegador cresce continuamente ao deixar o dashboard aberto.'],
                ['Corrigir cálculo incorreto de XP em desafios', 'O bônus de desafio às vezes soma um valor diferente do configurado.'],
                ['Ajustar quebra de layout no Safari', 'Os cards do Kanban ficam desalinhados no Safari em telas menores.'],
                ['Corrigir duplicidade de notificações toast', 'Em alguns casos o mesmo toast de conquista aparece duas vezes.'],
                ['Resolver erro 500 ao anexar imagem grande', 'Upload de foto de perfil acima de 2MB derruba a requisição.'],
                ['Corrigir condição de corrida no drag-and-drop', 'Mover duas tarefas rapidamente pode deixar a posição inconsistente.'],
            ],
            'Feature' => [
                ['Implementar exportação de relatórios em PDF', 'Gestores pediram poder exportar o relatório de produtividade do time.'],
                ['Adicionar autenticação em dois fatores', 'Reforçar a segurança de login para contas administrativas.'],
                ['Criar dashboard de métricas para gestores', 'Visão consolidada de XP, tarefas concluídas e prazos por equipe.'],
                ['Implementar busca global no sistema', 'Buscar tarefas, pessoas e conquistas a partir de um campo único.'],
                ['Adicionar visualização em lista para o Kanban', 'Alternativa ao quadro para quem prefere uma lista densa de tarefas.'],
                ['Criar sistema de notificações por e-mail', 'Avisar por e-mail quando uma tarefa for atribuída ou aprovada.'],
                ['Implementar integração com Slack', 'Enviar notificações de conquistas e nível para um canal do Slack.'],
            ],
            'Improvement' => [
                ['Otimizar tempo de carregamento do dashboard', 'Reduzir o número de consultas ao banco na tela inicial.'],
                ['Melhorar responsividade das tabelas administrativas', 'Tabelas grandes ainda quebram em telas de tablet.'],
                ['Refinar mensagens de validação dos formulários', 'Deixar as mensagens de erro mais claras e específicas.'],
                ['Adicionar paginação na listagem de conquistas', 'A grade de conquistas fica muito longa com muitos itens.'],
                ['Melhorar acessibilidade dos formulários', 'Adicionar labels e foco visível em todos os campos.'],
            ],
            'Infrastructure' => [
                ['Configurar pipeline de CI/CD', 'Rodar testes e lint automaticamente a cada push.'],
                ['Migrar armazenamento de arquivos para S3', 'Fotos de perfil e logos ainda ficam em disco local.'],
                ['Configurar backup automático do banco de dados', 'Nenhum backup agendado existe hoje em produção.'],
                ['Implementar cache com Redis', 'Reduzir carga do banco em consultas de ranking repetidas.'],
                ['Configurar monitoramento de erros com Sentry', 'Hoje erros em produção só aparecem no log do servidor.'],
            ],
            'Refactoring' => [
                ['Refatorar serviço de cálculo de XP', 'A lógica de bônus está espalhada entre vários listeners.'],
                ['Extrair lógica de permissões para Policies dedicadas', 'Algumas checagens de acesso ainda estão direto no componente.'],
                ['Reorganizar estrutura de componentes Livewire', 'Padronizar a nomenclatura entre os módulos administrativos.'],
                ['Simplificar consultas N+1 no ranking', 'A tela de ranking ainda dispara uma consulta por usuário.'],
            ],
            'Documentation' => [
                ['Documentar API REST interna', 'Faltam exemplos de uso para os endpoints já existentes.'],
                ['Criar guia de onboarding para novos desenvolvedores', 'Novos membros do time perdem tempo descobrindo o padrão do projeto.'],
                ['Atualizar README com instruções de deploy', 'O passo a passo de deploy está desatualizado.'],
                ['Documentar regras de negócio de gamificação', 'As regras de XP e conquistas só existem na cabeça de quem implementou.'],
            ],
        ];
    }

    /**
     * @param  array<string, array<int, array{0: string, 1: string}>>  $pool
     * @return array{0: string, 1: string}
     */
    private function pickTitle(array $pool, string $category): array
    {
        $options = $pool[$category];

        return $options[array_rand($options)];
    }

    private function validCpf(string $base): string
    {
        $digits = $base;

        for ($round = 0; $round < 2; $round++) {
            $length = strlen($digits);
            $sum = 0;

            for ($i = 0; $i < $length; $i++) {
                $sum += (int) $digits[$i] * (($length + 1) - $i);
            }

            $check = ($sum * 10) % 11;
            $digits .= (string) ($check === 10 ? 0 : $check);
        }

        return $digits;
    }
}
