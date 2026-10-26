# Dev Quest Gamify

Plataforma de gestão de tarefas para equipes de desenvolvimento com sistema de gamificação integrado: quadro Kanban, XP, níveis, ranking, conquistas, títulos, desafios e check-in diário.

## Stack

- PHP 8.3+
- Laravel 13
- Alpine.js (interações no frontend, consumindo a API JSON interna)
- Bootstrap 5.3 + Bootstrap Icons + Vite
- MySQL 8
- Spatie Laravel Permission (RBAC)
- PHPUnit
- Laravel Pint (lint) / Larastan (análise estática)

## Arquitetura

O backend segue uma separação rígida em camadas. Cada camada só conversa com a seguinte:

```
Request -> Controller -> Service -> Repository -> Model -> Migration
```

- **Migration** (`database/migrations`): estrutura do banco.
- **Model** (`app/Models`): mapeamento de tabela, casts, accessors/mutators e relacionamentos. Sem queries de negócio.
- **Repository** (`app/Repositories`): único lugar que monta queries e acessa o banco, sempre passando pela Model.
- **Service** (`app/Services`): regras de negócio, orquestração, transações e disparo de eventos.
- **Controller / Middleware** (`app/Http/Controllers`, `app/Http/Middleware`): só orquestram a requisição e travam permissões (policies e `role:`). Sem regra de negócio e sem query.
- **Request** (`app/Http/Requests`): atua como DTO de entrada; dado inválido é recusado antes de chegar ao Controller.
- **Resource** (`app/Http/Resources`): DTO de saída; define o formato JSON devolvido.

Ao adicionar uma funcionalidade nova, mantenha essa cadeia. Não chame `Model::query()` a partir de um Controller ou de um Service.

### API e frontend

- A API JSON fica em `routes/api.php` (prefixo `/api`) e usa a sessão + CSRF do próprio sistema (grupo `web` + `auth`); não há tokens no JavaScript.
- Páginas (`routes/web.php`) são servidas por Controllers de página, que passam os dados já preparados pelo Service para a view Blade.
- O JavaScript (`resources/js`) chama a API pelo cliente `api.js`. O Alpine.js cuida de modais, filtros e drag-and-drop; ações que alteram o estado da página guardam os avisos e recarregam a tela.
- Erros da API sempre trazem `message` específico e o código HTTP adequado (401, 403, 404, 409, 422); o frontend exibe apenas essa mensagem. Toasts de gamificação (level up, conquistas etc.) viajam no campo `toasts` da resposta.

Outros pontos relevantes:

- A navegação principal fica em uma sidebar fixa (offcanvas no mobile), definida em `components/layouts/app.blade.php`. Telas administrativas ficam centralizadas em `/admin` (`AdminHubPageController`).
- A UI usa apenas Bootstrap 5.3 (classes utilitárias, componentes e Bootstrap Icons). A cor primária é configurável em Configurações > Identidade da empresa (`app_settings.primary_color`) e aplicada em runtime pelo componente `x-theme-style`, que sobrescreve as variáveis CSS `--bs-primary*`.
- Autorização usa `spatie/laravel-permission`. Há um `Gate::before` global em [AppServiceProvider.php](app/Providers/AppServiceProvider.php) que libera qualquer ability para quem tem a role `admin`, exceto a ability `move` (que tem regra estrutural própria e não deve ser bypassada por permissão).
- O quadro Kanban usa colunas livres por board (estilo Trello), não um pipeline fixo de status. Tarefas podem ser arquivadas individualmente por admin/PO; cada board tem uma página de arquivo separada em concluídas/não concluídas.

## Requisitos

- PHP 8.3 ou superior, com as extensões exigidas pelo Laravel 13
- Composer 2
- Node.js 22 e npm
- MySQL 8 (ou compatível)
- Docker Desktop, caso opte pelo ambiente containerizado

## Instalação (ambiente local, sem Docker)

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure `DB_*` no `.env` apontando para um MySQL acessível localmente e crie o banco de dados definido em `DB_DATABASE`. Em seguida:

```bash
php artisan migrate --seed
npm install
npm run build
```

Para desenvolvimento com hot reload, servidor HTTP e worker de fila juntos:

```bash
composer dev
```

Esse comando sobe `php artisan serve`, `php artisan queue:listen` e `npm run dev` em paralelo.

Alternativamente, `composer setup` executa instalação de dependências, geração de key, migrations e build de assets em um único passo (idempotente, útil para primeira configuração).

## Instalação (Docker / Laravel Sail)

O projeto já inclui `compose.yaml` com os serviços `laravel.test` (PHP 8.3), `mysql`, `redis` e `mailpit`.

```bash
docker compose up -d
docker compose exec laravel.test composer install
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate --seed
docker compose exec laravel.test npm install
docker compose exec laravel.test npm run build
```

Observações:

- O wrapper `./vendor/bin/sail` (script bash) só reconhece macOS, Linux e WSL2. Em Windows fora do WSL2 (por exemplo Git Bash), ele falha com `Unsupported operating system` — use `docker compose ...` diretamente, como acima, ou rode dentro do WSL2 para ter os atalhos `sail artisan`, `sail composer`, etc.
- O `.env` usado pelo Docker aponta para os hostnames internos do compose (`DB_HOST=mysql`, `REDIS_HOST=redis`, `MAIL_HOST=mailpit`). Ele não funciona para rodar a aplicação fora dos containers; para isso, ajuste esses valores de volta para `127.0.0.1`.
- As portas expostas por padrão neste projeto foram deslocadas das portas padrão do Sail (`APP_PORT=8080`, `FORWARD_DB_PORT=3307`, `FORWARD_REDIS_PORT=6380`, `FORWARD_MAILPIT_PORT=1026`, `FORWARD_MAILPIT_DASHBOARD_PORT=8026`) para não colidir com serviços já rodando na máquina (ex.: MySQL local na 3306). Ajuste essas variáveis no `.env` se não houver conflito no seu ambiente.
- Aplicação acessível em `http://localhost:8080` (ou na porta configurada em `APP_PORT`). Mailpit em `http://localhost:8026`.

## Variáveis de ambiente relevantes

Além do padrão do Laravel, vale destacar:

| Variável | Descrição |
|---|---|
| `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Conexão MySQL. Em Docker, `DB_HOST=mysql`. |
| `REDIS_HOST` | `redis` em Docker, `127.0.0.1` em ambiente local. |
| `QUEUE_CONNECTION` | `database` por padrão; há jobs de gamificação processados via fila. |
| `SESSION_DRIVER`, `CACHE_STORE` | `database` por padrão. |

O arquivo `.env.example` reflete a configuração padrão para desenvolvimento local (fora de containers).

## Seeders

`php artisan migrate --seed` executa `DatabaseSeeder`, que popula:

- Roles e permissions (`admin`, `product_owner`, `dev`, `tester`, `suporte`) via `RoleSeeder`
- Níveis de XP, categorias e prioridades de tarefa
- Regras de eventos de tarefa (pontuação)
- Conquistas, desafios (semana, mês, encerrado e agendado) e títulos desbloqueados por conquista, por desafio específico ou por quantidade de desafios
- 5 quadros (um inativo) com colunas padrão
- Usuários de exemplo (`UserSeeder`) e dados de demonstração (`DemoDataSeeder`): cerca de 20 usuários de todas as roles, ~140 tarefas distribuídas pelos quadros (concluídas, arquivadas, em andamento, reprovadas e atrasadas), histórico de XP dos últimos dias, check-ins com sequências, progresso de desafios e títulos já desbloqueados

Todos os usuários de exemplo usam a senha `password` (ex.: `admin@devquestgamify.test`, `dev@devquestgamify.test`).

## Testes e qualidade de código

```bash
composer test          # config:clear + lint:check + types:check + php artisan test
composer lint           # Pint, aplica correções
composer lint:check     # Pint, apenas verifica
composer types:check    # Larastan (phpstan)
```

Os testes usam SQLite em memória (`phpunit.xml`), independente do banco configurado no `.env` — não é necessário um MySQL disponível para rodar a suíte de testes.

Ao escrever testes que envolvem o Kanban, defina `status` explicitamente nas factories de `Task`. `TaskFactory` não deriva o status a partir da coluna, e testes que fazem apenas o *move* de uma tarefa entre colunas podem ficar com um status inconsistente com a coluna se `status` não for fixado.

O CI (`.github/workflows/tests.yml`) roda `composer setup` seguido de `composer ci:check` em cada push/PR para `main`.

## Estrutura de pastas

```
app/
  Enums/            Enumerações de domínio (status de tarefa, tipo de evento de XP, roles, etc.)
  Events/           Eventos de domínio
  Http/
    Controllers/    Controllers da API e das páginas, organizados por área (Admin, Board, Task, Gamification, Profile)
    Middleware/      Middlewares HTTP, incluindo checagem de role
    Requests/        Requests (validação de entrada, atuam como DTOs)
    Resources/       Resources (formato JSON de saída)
  Listeners/        Listeners de eventos
  Models/           Models Eloquent
  Policies/         Policies de autorização
  Repositories/     Camada de acesso a dados
  Rules/            Regras de validação customizadas
  Services/         Regras de negócio
  Support/          Classes utilitárias transversais (ex.: ToastCollector)
database/
  migrations/       Migrations
  seeders/          Seeders
  factories/        Factories para testes
resources/
  views/            Views Blade e componentes Blade
  js/               Cliente da API e componentes Alpine.js
routes/
  web.php           Páginas autenticadas e administrativas (role:admin)
  api.php           API JSON consumida pelo frontend (sessão + CSRF)
  auth.php          Rotas de autenticação
tests/
  Feature/          Testes de feature, incluindo área administrativa
  Unit/             Testes unitários (Enums, Models, Services)
docs/
  REQUISITOS.md     Levantamento de requisitos funcionais do produto
```

## Autorização

Guard `web`, gerenciado por `spatie/laravel-permission`. Roles seedadas: `admin`, `product_owner`, `dev`, `tester`, `suporte`, cada uma com um conjunto fixo de permissions definido em [RoleSeeder.php](database/seeders/RoleSeeder.php).

Rotas administrativas usam o middleware `role:admin`. Regras de visibilidade mais finas (por exemplo, o que um `dev` enxerga no Kanban) ficam na camada de Service/Repository, não apenas no middleware de rota.

## Documentação adicional

O levantamento de requisitos funcionais do produto está em [docs/REQUISITOS.md](docs/REQUISITOS.md).
