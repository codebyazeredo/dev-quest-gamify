# Dev Quest Gamify

Plataforma de gestão de tarefas para equipes de desenvolvimento com sistema de gamificação integrado: quadro Kanban, XP, níveis, ranking, conquistas, títulos, desafios e check-in diário.

## Stack

- PHP 8.3+
- Laravel 13
- Livewire 4 (Blaze)
- Tailwind CSS 4 + Vite
- MySQL 8
- Spatie Laravel Permission (RBAC)
- PHPUnit
- Laravel Pint (lint) / Larastan (análise estática)

## Arquitetura

O código de domínio segue uma separação em camadas. Componentes Livewire não acessam Eloquent diretamente.

```
Livewire (app/Livewire)  ->  Service (app/Services)  ->  Repository (app/Repositories)  ->  Model (app/Models)
```

- **Livewire**: recebe input do usuário, chama Services, expõe estado para a view. Sem regra de negócio e sem query direta.
- **Service**: regra de negócio, orquestração, transações.
- **Repository**: acesso a dados via Eloquent. É a única camada que deve montar queries.
- **Model**: mapeamento de tabela, relacionamentos, casts, escopos simples.

Ao adicionar uma funcionalidade nova, mantenha essa cadeia. Não chame `Model::query()` a partir de um componente Livewire ou de um controller.

Outros pontos relevantes:

- Não há sidebar. A navegação principal fica em ícones na navbar. Telas administrativas ficam centralizadas em `/admin` (`AdminHub`).
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
- Conquistas, títulos e desafios
- Board e colunas padrão
- Usuários de exemplo (`UserSeeder`) e, opcionalmente, dados de demonstração (`DemoDataSeeder`)

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
    Controllers/    Controllers (uso mínimo; a maior parte da aplicação é Livewire)
    Middleware/      Middlewares HTTP, incluindo checagem de role
    Requests/        Form Requests
  Listeners/        Listeners de eventos
  Livewire/         Componentes Livewire, organizados por área (Admin, Board, Task, Dashboard, Gamification, Checkin, Profile)
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
  views/            Views Blade, incluindo templates dos componentes Livewire
routes/
  web.php           Rotas autenticadas e rotas administrativas (role:admin)
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
