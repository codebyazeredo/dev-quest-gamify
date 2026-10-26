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
        $this->seedSelectedTitles();

        Carbon::setTestNow();
    }

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
            ['nome' => 'Gabriel Martins Teixeira', 'email' => 'gabriel.teixeira@devquestgamify.test', 'role' => 'dev', 'cpfBase' => '909010111', 'sexo' => 1],
            ['nome' => 'Fernanda Lopes Dias', 'email' => 'fernanda.dias@devquestgamify.test', 'role' => 'dev', 'cpfBase' => '010111212', 'sexo' => 2],
            ['nome' => 'Lucas Araújo Moreira', 'email' => 'lucas.moreira@devquestgamify.test', 'role' => 'dev', 'cpfBase' => '111212313', 'sexo' => 1],
            ['nome' => 'Aline Batista Correia', 'email' => 'aline.correia@devquestgamify.test', 'role' => 'dev', 'cpfBase' => '414515616', 'sexo' => 2],
            ['nome' => 'Patrícia Mendes Duarte', 'email' => 'patricia.duarte@devquestgamify.test', 'role' => 'tester', 'cpfBase' => '212313414', 'sexo' => 2],
            ['nome' => 'Vinícius Campos Freitas', 'email' => 'vinicius.freitas@devquestgamify.test', 'role' => 'suporte', 'cpfBase' => '313414515', 'sexo' => 1],
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

        $usersByRole['dev'][] = User::where('email', 'dev@devquestgamify.test')->first();
        $usersByRole['tester'][] = User::where('email', 'tester@devquestgamify.test')->first();
        $usersByRole['suporte'][] = User::where('email', 'suporte@devquestgamify.test')->first();
        $usersByRole['product_owner'][] = User::where('email', 'po@devquestgamify.test')->first();

        return $usersByRole;
    }

    private function seedCheckins(array $users): void
    {
        $streaks = [
            [$users['dev'][0] ?? null, 12],
            [$users['dev'][1] ?? null, 6],
            [$users['tester'][0] ?? null, 4],
            [$users['suporte'][0] ?? null, 8],
            [$users['dev'][2] ?? null, 1],
            [$users['dev'][3] ?? null, 15],
            [$users['dev'][4] ?? null, 9],
            [$users['dev'][5] ?? null, 5],
            [$users['dev'][6] ?? null, 3],
            [$users['tester'][1] ?? null, 7],
            [$users['tester'][2] ?? null, 2],
            [$users['suporte'][1] ?? null, 10],
            [$users['suporte'][2] ?? null, 4],
            [$users['product_owner'][0] ?? null, 6],
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

    private function boardPlans(): array
    {
        return [
            'Sistema Principal' => [
                'old' => 22, 'archived' => 8, 'recent' => 10, 'rejected' => 3,
                'stages' => [
                    TaskStatus::BACKLOG->value => 10,
                    TaskStatus::TODO->value => 7,
                    TaskStatus::DOING->value => 6,
                    TaskStatus::REVIEW->value => 4,
                    TaskStatus::TESTING->value => 4,
                    TaskStatus::APPROVED->value => 3,
                ],
            ],
            'Aplicativo Mobile' => [
                'old' => 8, 'archived' => 2, 'recent' => 4, 'rejected' => 1,
                'stages' => [
                    TaskStatus::BACKLOG->value => 5,
                    TaskStatus::TODO->value => 4,
                    TaskStatus::DOING->value => 3,
                    TaskStatus::REVIEW->value => 2,
                    TaskStatus::TESTING->value => 2,
                    TaskStatus::APPROVED->value => 1,
                ],
            ],
            'Portal do Cliente' => [
                'old' => 6, 'archived' => 1, 'recent' => 3, 'rejected' => 1,
                'stages' => [
                    TaskStatus::BACKLOG->value => 4,
                    TaskStatus::TODO->value => 3,
                    TaskStatus::DOING->value => 3,
                    TaskStatus::REVIEW->value => 1,
                    TaskStatus::TESTING->value => 1,
                    TaskStatus::APPROVED->value => 1,
                ],
            ],
            'Infraestrutura e DevOps' => [
                'old' => 5, 'archived' => 1, 'recent' => 2, 'rejected' => 0,
                'stages' => [
                    TaskStatus::BACKLOG->value => 3,
                    TaskStatus::TODO->value => 2,
                    TaskStatus::DOING->value => 2,
                    TaskStatus::REVIEW->value => 1,
                    TaskStatus::TESTING->value => 1,
                    TaskStatus::APPROVED->value => 0,
                ],
            ],
        ];
    }

    private function seedTasks(array $users): void
    {
        $categories = TaskCategory::pluck('id', 'name');
        $priorities = TaskPriority::orderBy('multiplier')->get();
        $pool = $this->titlePool();

        foreach ($this->boardPlans() as $boardName => $plan) {
            $board = Board::where('name', $boardName)->first();

            if ($board === null) {
                continue;
            }

            $context = [
                'columns' => BoardColumn::where('board_id', $board->id)->get()->keyBy(fn ($c) => $c->status->value),
                'categories' => $categories,
                'priorities' => $priorities,
                'pool' => $pool,
                'users' => $users,
            ];

            $this->seedFinishedTasks($context, $plan['old'], $plan['archived']);
            $this->seedRecentlyFinishedTasks($context, $plan['recent']);
            $this->seedInProgressTasks($context, $plan['stages']);
            $this->seedRejectedTasks($context, $plan['rejected']);
        }
    }

    private function seedFinishedTasks(array $context, int $count, int $archivedCount): void
    {
        for ($i = 0; $i < $count; $i++) {
            $task = $this->runFullFlow($context, (rand(2, 21) + 4) * 24, 6);

            if ($i < $archivedCount) {
                $this->taskService->archive($task);
            }
        }
    }

    private function seedRecentlyFinishedTasks(array $context, int $count): void
    {
        $devs = $context['users']['dev'];

        for ($i = 0; $i < $count; $i++) {
            $dev = $devs[$i % 2];
            $category = $i % 2 === 0 ? 'Bug' : array_rand($context['pool']);

            $this->runFullFlow($context, rand(0, 6) * 24 + 9, 1, $dev, $category);
        }
    }

    private function runFullFlow(array $context, int $hoursAgo, int $dueOffsetDays, ?User $dev = null, ?string $category = null): Task
    {
        $columns = $context['columns'];
        $users = $context['users'];
        $creators = [...$users['suporte'], $users['product_owner'][0]];

        $category ??= array_rand($context['pool']);
        [$title, $description] = $this->pickTitle($context['pool'], $category);
        $dev ??= $users['dev'][array_rand($users['dev'])];
        $tester = $users['tester'][array_rand($users['tester'])];
        $creator = $creators[array_rand($creators)];
        $priority = $context['priorities'][array_rand($context['priorities']->all())];

        Carbon::setTestNow(now()->subHours($hoursAgo));

        $task = $this->taskService->create([
            'title' => $title,
            'description' => $description,
            'category_id' => $context['categories'][$category],
            'priority_id' => $priority->id,
            'column_id' => $columns[TaskStatus::BACKLOG->value]->id,
            'assigned_to' => $dev->id,
            'due_at' => now()->addDays($dueOffsetDays + 3)->format('Y-m-d H:i:s'),
        ], $creator);

        $this->taskService->move($task, $columns[TaskStatus::TODO->value], 0, $creator);
        $this->taskService->move($task, $columns[TaskStatus::DOING->value], 0, $dev);
        Carbon::setTestNow(now()->addHours(2));
        $this->taskService->move($task, $columns[TaskStatus::REVIEW->value], 0, $dev);
        Carbon::setTestNow(now()->addHours(2));
        $this->taskService->move($task, $columns[TaskStatus::TESTING->value], 0, $tester);
        Carbon::setTestNow(now()->addHours(1));
        $this->taskService->approve($task, $tester);
        Carbon::setTestNow(now()->addHours(1));
        $this->taskService->markHomologationCompleted($task, $dev);
        Carbon::setTestNow(now()->addHours(1));
        $this->taskService->markDeployed($task, $dev);

        Carbon::setTestNow();

        return $task->refresh();
    }

    private function seedInProgressTasks(array $context, array $stages): void
    {
        $columns = $context['columns'];
        $users = $context['users'];
        $devs = $users['dev'];
        $testers = $users['tester'];
        $creators = [...$users['suporte'], $users['product_owner'][0]];
        $dueOffsets = [-6, -3, -1, 1, 2, 4, 7, 14, 21, null, null];

        foreach ($stages as $statusValue => $count) {
            for ($i = 0; $i < $count; $i++) {
                $category = array_rand($context['pool']);
                [$title, $description] = $this->pickTitle($context['pool'], $category);
                $priority = $context['priorities'][array_rand($context['priorities']->all())];
                $creator = $creators[array_rand($creators)];

                $assignTo = $statusValue === TaskStatus::BACKLOG->value && rand(0, 1) === 0
                    ? null
                    : $devs[array_rand($devs)]->id;

                $offset = $dueOffsets[array_rand($dueOffsets)];
                $dueAt = $offset === null ? null : now()->addDays($offset)->format('Y-m-d H:i:s');

                $task = $this->taskService->create([
                    'title' => $title,
                    'description' => $description,
                    'category_id' => $context['categories'][$category],
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

    private function seedRejectedTasks(array $context, int $count): void
    {
        $reasons = [
            'O cenário de borda com valores vazios ainda quebra a tela.',
            'Faltou cobrir o caso de usuário sem permissão.',
            'O comportamento no celular não segue o que foi combinado.',
        ];

        $columns = $context['columns'];
        $users = $context['users'];
        $creators = [...$users['suporte'], $users['product_owner'][0]];

        for ($i = 0; $i < $count; $i++) {
            $category = array_rand($context['pool']);
            [$title, $description] = $this->pickTitle($context['pool'], $category);
            $dev = $users['dev'][array_rand($users['dev'])];
            $tester = $users['tester'][array_rand($users['tester'])];
            $priority = $context['priorities'][array_rand($context['priorities']->all())];

            $task = $this->taskService->create([
                'title' => $title,
                'description' => $description,
                'category_id' => $context['categories'][$category],
                'priority_id' => $priority->id,
                'column_id' => $columns[TaskStatus::BACKLOG->value]->id,
                'assigned_to' => $dev->id,
                'due_at' => now()->addDays(rand(2, 8))->format('Y-m-d H:i:s'),
            ], $creators[array_rand($creators)]);

            $this->taskService->move($task, $columns[TaskStatus::TODO->value], 0, $dev);
            $this->taskService->move($task, $columns[TaskStatus::DOING->value], 0, $dev);
            $this->taskService->move($task, $columns[TaskStatus::REVIEW->value], 0, $dev);
            $this->taskService->move($task, $columns[TaskStatus::TESTING->value], 0, $tester);
            $this->taskService->reject($task, $tester, $reasons[$i % count($reasons)]);
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
