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

- PHP 8.3 ou superior, com as extensões exigidas pelo Laravel 13 (incluindo `pdo_mysql`)
- Composer 2
- Node.js 22 ou superior e npm
- Docker Desktop (ou Docker Engine com Compose v2), para MySQL, Redis e Mailpit

## Instalação

A aplicação roda direto na máquina; o Docker sobe apenas a infraestrutura (MySQL 8.4, Redis e Mailpit). Isso evita a lentidão de montar o código em um container, principalmente no Windows.

```bash
cp .env.example .env
docker compose up -d mysql redis mailpit
composer install
npm install
php artisan migrate --seed
npm run build
```

O `.env.example` já vem configurado para essa infraestrutura, com MySQL `root`/`root`, banco `dev_quest_gamify` e as portas deslocadas. Se precisar de outra `APP_KEY`, rode `php artisan key:generate`.

Para desenvolvimento, com servidor HTTP, worker de fila e Vite (hot reload) juntos:

```bash
composer dev
```

Acessos:

- Aplicação: `http://localhost:8000`
- Mailpit: `http://localhost:8026`
- MySQL (DBeaver, etc.): `localhost:3307`, usuário `root`, senha `root`

Comandos úteis:

```bash
php artisan test
docker compose stop
docker compose down -v
```

`docker compose down -v` remove também o volume do MySQL e recria o banco do zero.

Observações:

- As portas da infraestrutura foram deslocadas das padrão (`FORWARD_DB_PORT=3307`, `FORWARD_REDIS_PORT=6380`, `FORWARD_MAILPIT_PORT=1026`, `FORWARD_MAILPIT_DASHBOARD_PORT=8026`) para não colidir com serviços locais. Ao alterar uma delas no `.env`, ajuste também `DB_PORT`, `REDIS_PORT` ou `MAIL_PORT`.
- Os testes usam SQLite em memória (`phpunit.xml`) e não dependem do MySQL.
- O `compose.yaml` também define o serviço `app` (Laravel Sail, PHP 8.3 + Node 22) para quem não tem PHP instalado: `docker compose up -d --build` e `docker compose exec app <comando>`. Em Windows isso é bem mais lento por causa do bind mount.

## Variáveis de ambiente relevantes

Além do padrão do Laravel, vale destacar:

| Variável | Descrição |
|---|---|
| `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Conexão MySQL. Padrão: `127.0.0.1:3307`, `root`/`root` (container publicado no host). |
| `REDIS_HOST` | `127.0.0.1` (container publicado na porta `6380`). |
| `QUEUE_CONNECTION` | `database` por padrão; há jobs de gamificação processados via fila. |
| `SESSION_DRIVER`, `CACHE_STORE` | `database` por padrão. |

O arquivo `.env.example` reflete a configuração padrão para desenvolvimento com a infraestrutura em Docker.

## Seeders

`php artisan migrate --seed` executa `DatabaseSeeder`, que monta o cenário de uma empresa fictícia, a **RotaMax Sistemas**, fabricante de um TMS (sistema de gestão de transporte):

- Configuração: roles e permissions, níveis de XP, categorias, prioridades, regras de XP, conquistas, desafios e títulos.
- Equipe (8 pessoas): 1 admin (Ricardo), 1 product owner (Camila), 3 desenvolvedores de senioridades diferentes (Rafael, sênior; Lucas, pleno; Thiago, júnior), 1 testadora (Patrícia) e 2 do suporte (Mariana e Diego).
- 2 quadros: **TMS Core** e **Integrações e Plataforma**, com cerca de 55 tarefas em todas as etapas, histórico de XP, check-ins, comentários e checklists.
- 23 clientes (transportadoras, embarcadores, operadores logísticos) e cerca de 58 chamados com respostas, notas internas, chamados convertidos em tarefa, aguardando cliente, em andamento e com prazo vencido.

Contas de exemplo, todas com a senha `password`: `admin@`, `po@`, `dev@` (pleno), `tester@` e `suporte@` + `devquestgamify.test`; os outros são `rafael.monteiro@`, `thiago.barros@` e `diego.martins@`. O portal do cliente aceita o CNPJ e o e-mail de qualquer cliente cadastrado (e-mail no formato `operacoes@<empresa>.test`).

O `DemoDataSeeder` reproduz o fluxo real das tarefas com data retroativa, por isso o seed completo leva alguns minutos no MySQL em Docker (no SQLite em memória dos testes leva segundos). Os dados ficam em `database/seeders/Data/TmsScenario.php` e `TicketSeeder.php`.

## Testes e qualidade de código

```bash
composer test          # config:clear + lint:check + types:check + php artisan test
composer lint           # Pint, aplica correções
composer lint:check     # Pint, apenas verifica
composer types:check    # Larastan (phpstan)
```

Os testes usam SQLite em memória (`phpunit.xml`), independente do banco configurado no `.env` — não é necessário um MySQL disponível para rodar a suíte de testes.

Ao escrever testes que envolvem o Kanban, defina `status` explicitamente nas factories de `Task`. `TaskFactory` não deriva o status a partir da coluna, e testes que fazem apenas o *move* de uma tarefa entre colunas podem ficar com um status inconsistente com a coluna se `status` não for fixado.

O CI (`.github/workflows/tests.yml`, com SQLite) roda `composer setup` seguido de `composer ci:check` em cada push/PR para `main`.

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
