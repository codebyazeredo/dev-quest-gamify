# DevQuestGamify — Levantamento de Requisitos

## 1. Visão geral

O DevGamify será uma plataforma de gestão de tarefas para equipes de desenvolvimento de software com um sistema integrado de gamificação.

O sistema combina:

* Kanban;
* gerenciamento de tarefas;
* pontuação por atividades;
* XP;
* níveis;
* ranking;
* conquistas;
* títulos;
* desafios;
* check-in diário;
* sequência de atividades;
* recompensas.

A aplicação será construída inteiramente em Laravel 13.

### Stack

```text
PHP 8.3+
Laravel 13
Blade
Livewire
Alpine.js
Tailwind CSS
MySQL
Eloquent ORM
Vite
PHPUnit / Pest
Laravel Pint
```

O Livewire será responsável pela camada interativa da aplicação, permitindo construir componentes reativos diretamente com PHP + Blade, sem a necessidade de um framework SPA separado.

## 2. Objetivo do sistema

O objetivo é transformar atividades normais de desenvolvimento em uma experiência de progressão.

Um desenvolvedor não apenas:

"resolveu um bug"

Ele:

```text
Resolveu bug crítico
        ↓
+50 XP
        ↓
Subiu de nível
        ↓
Desbloqueou conquista
        ↓
Avançou em um desafio
        ↓
Subiu no ranking
```

A gamificação deverá incentivar:

* produtividade;
* consistência;
* colaboração;
* qualidade;
* conclusão de tarefas;
* participação no processo;
* evolução profissional.

## 3. Princípio principal

O sistema deverá separar claramente duas áreas:

```text
┌──────────────────────────────┐
│          PRODUTO             │
│                              │
│ Boards                       │
│ Columns                      │
│ Tasks                        │
│ Categories                   │
│ Task Events                  │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│        GAMIFICAÇÃO            │
│                              │
│ XP                           │
│ Levels                       │
│ Ranking                      │
│ Achievements                 │
│ Titles                       │
│ Challenges                   │
│ Check-ins                    │
└──────────────────────────────┘
```

A gestão de tarefas não deverá ficar acoplada diretamente às regras de gamificação.

## 4. Arquitetura

A aplicação será um monólito Laravel.

```text
Laravel
│
├── HTTP
│   ├── Routes
│   ├── Controllers
│   ├── Requests
│   └── Middleware
│
├── Presentation
│   ├── Blade
│   ├── Livewire
│   └── Blade Components
│
├── Application
│   ├── Services
│   ├── Actions
│   └── Events
│
├── Domain
│   ├── Models
│   ├── Enums
│   └── Rules
│
└── Infrastructure
    └── Eloquent / MySQL
```

## 5. Frontend

Não haverá:

```text
Angular
React
Vue SPA
API REST para o frontend
```

O frontend será:

```text
Blade
 +
Livewire
 +
Alpine.js
 +
Tailwind CSS
```

Livewire permite que componentes sejam utilizados dentro de Blade ou como páginas completas, mantendo a interação no lado servidor.

## 6. Blade

Blade será utilizado para:

* layouts;
* componentes visuais;
* menus;
* sidebar;
* navbar;
* modais simples;
* cards;
* badges;
* tabelas;
* elementos estáticos.

Exemplo:

```text
resources/views/
├── layouts/
│   ├── app.blade.php
│   └── guest.blade.php
│
├── components/
│   ├── button.blade.php
│   ├── badge.blade.php
│   ├── modal.blade.php
│   └── card.blade.php
│
└── pages/
```

## 7. Livewire

Livewire será utilizado quando existir comportamento dinâmico.

Exemplos:

```text
Dashboard
Kanban
TaskModal
TaskDetails
TaskFilters
Ranking
Achievements
Titles
Challenges
Checkin
Notifications
```

Não devemos transformar todo HTML em Livewire.

A regra será:

Se o componente precisa reagir a ações do usuário ou atualizar dados sem recarregar a página, considerar Livewire. Caso contrário, utilizar Blade Component.

Essa separação também é recomendada pela própria documentação do Livewire.

## 8. Layout

A aplicação terá dois layouts principais.

### Guest

Para:

```text
Login
Recuperação de senha
```

### Application

Para usuários autenticados:

```text
Navbar
Sidebar
Content
Notifications
User menu
```

Estrutura:

```text
┌───────────────────────────────────────────────┐
│ NAVBAR                         XP / USER      │
├───────────────┬───────────────────────────────┤
│               │                               │
│ Dashboard     │                               │
│               │                               │
│ Kanban        │        CONTENT                │
│               │                               │
│ Ranking       │                               │
│               │                               │
│ Achievements  │                               │
│               │                               │
│ Titles        │                               │
│               │                               │
│ Challenges    │                               │
│               │                               │
│ Check-in      │                               │
│               │                               │
│ Admin         │                               │
│               │                               │
└───────────────┴───────────────────────────────┘
```

## 9. Autenticação

O sistema terá autenticação tradicional.

Funcionalidades:

* login;
* logout;
* sessão;
* recuperação de senha;
* usuário autenticado;
* autorização por role.

Perfis (Inicialmente, ajuste conforme necessario):

```text
ADMIN (Gestao completa, administrador geral do sistema)
PRODUCT_OWNER (pode criar e editar tarefas)
DEVELOPER (executa, revisa, e bota na fila de merge, porem nao pode revisar a propria tarefa)
```

## 10. Admin

O Admin será responsável pela configuração do ambiente.

Pode:

* criar boards;
* criar colunas;
* criar categorias;
* configurar pontuação;
* criar tarefas;
* atribuir tarefas;
* administrar usuários;
* configurar XP;
* criar achievements;
* criar títulos;
* criar desafios;
* consultar ranking;
* visualizar histórico;
* ajustar XP manualmente.

## 11. Developer

O Developer poderá:

* acessar o dashboard;
* visualizar o Kanban;
* visualizar tarefas;
* assumir tarefas;
* executar tarefas;
* mover tarefas;
* registrar eventos;
* realizar check-in;
* consultar XP;
* consultar nível;
* consultar ranking;
* visualizar conquistas;
* selecionar títulos;
* participar de desafios.

Não poderá alterar configurações administrativas.

## 12. Dashboard

### Developer

O dashboard deverá apresentar:

**Perfil**

```text
Matheus Azeredo
🔥 Release Master

LEVEL 12

4.850 XP
```

**Progresso**

```text
4.850 / 6.000 XP

████████████████░░░░

1.150 XP para Level 13
```

**Estatísticas**

```text
Tarefas concluídas       27
XP nesta semana          850
Ranking                  #2
Streak                   🔥 5 dias
```

**Atividades recentes**

```text
+50 XP  Bug crítico #125
+20 XP  Deploy #124
+10 XP  Code Review #123
+1 XP   Check-in
```

**Conquistas**

```text
🐛 Bug Hunter
🚀 Release Master
⚡ Speed Coder
```

## 13. Dashboard administrativo

O Admin terá uma visão global.

```text
Developers                 12
Tasks abertas              34
Tasks concluídas hoje      21
XP distribuído hoje        1.420
```

Ranking:

```text
1º João
2º Matheus
3º Pedro
4º Lucas
```

Também deverá possuir indicadores de:

* tarefas por status;
* tarefas por categoria;
* tarefas por prioridade;
* XP distribuído;
* desenvolvedores ativos;
* evolução semanal.

## 14. Kanban

O sistema terá um mini Trello.

Exemplo:

```text
BACKLOG      TODO       DOING       REVIEW       TESTING       DONE

Bug #12      Task #23   Bug #18     Bug #15      Feature #8    Bug #10
Bug #13      Task #24   Task #20    Task #17     Bug #11       Feature #5
```

Cada tarefa será exibida como um card.

## 15. Drag and Drop

O Kanban deverá permitir movimentar tarefas entre colunas.

Exemplo:

```text
DOING
  ↓
REVIEW
```

Ao mover:

```text
Livewire
   ↓
TaskService
   ↓
validação
   ↓
TaskEvent
   ↓
gamificação
   ↓
atualização da interface
```

O frontend poderá utilizar Alpine.js para comportamentos locais de drag-and-drop, enquanto o Livewire realizará a persistência.

## 16. Boards

O Admin poderá criar boards.

Campos:

```text
Nome
Descrição
Ativo
```

Exemplo:

```text
Sistema Financeiro
Portal do Cliente
Aplicativo Mobile
Infraestrutura
```

## 17. Columns

Cada board terá suas próprias colunas.

Campos:

```text
board_id
name
slug
position
is_final
```

Exemplo:

```text
Backlog
Todo
Doing
Review
Testing
Done
```

A ordem será controlada por `position`.

## 18. Categorias

Categorias representarão o tipo da tarefa.

Exemplo:

```text
Bug
Feature
Refactoring
Improvement
Infrastructure
Documentation
```

Cada categoria terá uma pontuação base.

Exemplo:

```text
Bug               10 XP
Feature           20 XP
Refactoring       15 XP
Improvement       15 XP
Infrastructure    20 XP
Documentation      5 XP
```

Categorias poderão ser administradas pelo Admin.

## 19. Prioridades

Prioridade será PHP Enum `int-backed`.

```php
enum TaskPriority: int
{
    case LOW = 1;
    case NORMAL = 2;
    case HIGH = 3;
    case CRITICAL = 4;
}
```

No MySQL:

```text
TINYINT UNSIGNED
```

## 20. Multiplicador

Cada prioridade possuirá multiplicador.

Exemplo:

```text
LOW        1.00
NORMAL     1.50
HIGH       2.00
CRITICAL   5.00
```

Assim:

```text
Bug = 10

LOW
10 × 1 = 10

CRITICAL
10 × 5 = 50
```

## 21. Tasks

A tarefa terá:

```text
id
board_id
column_id
category_id
assigned_to
created_by
title
description
priority
status
position
base_points
priority_multiplier
estimated_points
started_at
completed_at
timestamps
```

Os valores utilizados no cálculo deverão ser congelados na tarefa.

Isso evita que uma alteração futura na configuração altere o valor de tarefas antigas.

## 22. Status

Também será um PHP Enum.

```php
enum TaskStatus: int
{
    case BACKLOG = 1;
    case TODO = 2;
    case DOING = 3;
    case REVIEW = 4;
    case TESTING = 5;
    case DONE = 6;
}
```

## 23. Eventos da tarefa

A execução da tarefa será dividida em eventos.

Exemplo:

```text
STARTED
DEVELOPMENT_COMPLETED
REVIEW_COMPLETED
TEST_COMPLETED
HOMOLOGATION_COMPLETED
DEPLOYED
COMPLETED
```

Cada evento poderá gerar XP.

## 24. XP dos eventos

Exemplo:

```text
Desenvolvimento concluído       +10 XP
Review concluído                +10 XP
Testes concluídos                +5 XP
Homologação concluída            +5 XP
Deploy                           +20 XP
```

Esses valores serão configuráveis pelo Admin.

## 25. Histórico de tarefas

Toda ação relevante deverá ser registrada.

Exemplo:

```text
08:30
Matheus iniciou a tarefa

10:15
Matheus concluiu desenvolvimento
+10 XP

14:20
Matheus concluiu review
+10 XP

16:45
Matheus realizou deploy
+20 XP
```

## 26. Sistema de XP

O XP será tratado como um ledger.

Tabela:

```text
xp_transactions
```

Cada entrada representa uma movimentação.

Exemplo:

```text
+50  Task #125
+10  Development
+10  Review
+20  Deploy
+1   Check-in
+5   Weekly Bonus
```

O saldo poderá ser calculado através das transações.

## 27. Idempotência

Uma ação não poderá conceder XP duas vezes.

Exemplo:

```text
Deploy
 ↓
+20 XP
```

Se a requisição for repetida:

```text
Deploy
 ↓
já processado
 ↓
não concede novamente
```

Essa regra será obrigatória.

## 28. Levels

O sistema terá níveis:

```text
1 → 50
```

O nível 50 deverá ser difícil de alcançar.

Exemplo conceitual:

```text
Level 1       0 XP
Level 2       100 XP
Level 3       250 XP
...
Level 50      250.000+ XP
```

Os valores definitivos serão definidos no `LevelSeeder`.

## 29. Level Up

Quando o XP ultrapassar o limite:

```text
XP
 ↓
LevelService
 ↓
novo nível?
 ↓
SIM
 ↓
LevelUp Event
```

A interface deverá exibir uma animação:

```text
╔══════════════════════╗
║      LEVEL UP!       ║
║                      ║
║       LEVEL 13       ║
║                      ║
║       🎉 🎉 🎉       ║
╚══════════════════════╝
```

## 30. Ranking

Ranking geral:

```text
1º João       8.920 XP
2º Matheus    7.450 XP
3º Pedro      6.890 XP
4º Lucas      5.120 XP
```

Posteriormente:

```text
Ranking semanal
Ranking mensal
Ranking por equipe
```

## 31. Check-in

O Developer poderá realizar um check-in diário.

```text
25/08/2026
✓ Check-in realizado

+1 XP
```

O banco deverá garantir:

```text
UNIQUE(user_id, date)
```

## 32. Streak

O sistema deverá contar dias consecutivos.

```text
Seg  ✓
Ter  ✓
Qua  ✓
Qui  ✓
Sex  ✓
```

Resultado:

```text
🔥 5 dias consecutivos

+5 XP
```

O bônus não poderá ser concedido duas vezes para a mesma sequência.

## 33. Achievements

Conquistas representarão objetivos especiais.

Exemplos:

```text
🐛 Bug Hunter
Resolva 10 bugs

🚀 Release Master
Faça 10 deploys

⚡ Speed Coder
Conclua 5 tarefas em um dia

🏆 First Blood
Conclua sua primeira tarefa
```

## 34. Regras dos Achievements

Cada achievement terá:

```text
name
slug
description
condition_type
condition_value
xp_reward
active
```

Exemplo:

```text
Bug Hunter

condition:
BUGS_RESOLVED

value:
10

reward:
100 XP
```

## 35. Titles

Títulos serão diferentes de achievements.

O achievement representa uma conquista.

O título representa uma identidade.

Exemplo:

```text
🐛 Bug Hunter
🚀 Release Master
🔥 Firefighter
⚡ Speed Coder
🏆 Code Warrior
```

O usuário poderá desbloquear vários.

Mas poderá selecionar somente um.

## 36. Perfil

O perfil deverá mostrar:

```text
Matheus Azeredo

🔥 Release Master

Level 12
4.850 XP

Tasks completed: 27
Achievements: 8
Titles: 5

🔥 12 day streak
Ranking #2
```

## 37. Challenges

Desafios serão objetivos temporários.

Exemplo:

```text
DESAFIO DA SEMANA

Complete 5 tarefas

3 / 5

████████████░░░░░

Reward
+100 XP
```

Outro:

```text
BUG WEEK

Resolver 10 bugs

+200 XP
```

## 38. Progresso dos desafios

Cada usuário terá seu progresso individual.

```text
Challenge
     ↓
UserChallenge
     ↓
progress
     ↓
target
```

Ao atingir:

```text
progress >= target
```

o desafio será concluído e a recompensa concedida.

## 39. Events e Listeners

O sistema utilizará eventos do Laravel para desacoplar as funcionalidades.

Exemplo:

```text
TaskCompleted
       │
       ├── AchievementListener
       │
       ├── ChallengeListener
       │
       └── LevelListener
```

Outro:

```text
TaskEventCreated
       │
       └── GrantXpListener
```

## 40. Services

Os Controllers e componentes Livewire não deverão concentrar regras de negócio.

Teremos Services como:

```text
TaskService
XpService
LevelService
AchievementService
TitleService
ChallengeService
CheckinService
BoardService
```

Exemplo:

```php
$taskService->complete(
    task: $task,
    user: $user
);
```

O Service cuidará da regra.

## 41. Transactions

Operações que envolvam múltiplas alterações deverão utilizar transações.

Exemplo:

```text
BEGIN

Atualiza Task
       ↓
Cria TaskEvent
       ↓
Cria XpTransaction
       ↓
Atualiza Challenge
       ↓
Verifica Achievement
       ↓
Verifica Level

COMMIT
```

Se ocorrer erro:

```text
ROLLBACK
```

## 42. Models

Models principais:

```text
User

Board
BoardColumn

TaskCategory
Task
TaskEvent
TaskEventRule

XpTransaction
Level

Achievement
UserAchievement

Title
UserTitle

DailyCheckin

Challenge
UserChallenge
```

## 43. Enums

Todos os valores fechados deverão ser `int-backed`.

```text
UserRole
TaskPriority
TaskStatus
TaskEventType
XpSourceType
AchievementConditionType
TitleRequirementType
ChallengeType
```

Exemplo:

```php
enum XpSourceType: int
{
    case TASK = 1;
    case TASK_EVENT = 2;
    case ACHIEVEMENT = 3;
    case CHECKIN = 4;
    case CHALLENGE = 5;
    case BONUS = 6;
    case PENALTY = 7;
    case ADMIN_ADJUSTMENT = 8;
}
```

No banco:

```text
TINYINT UNSIGNED
```

## 44. Banco de dados

Estrutura inicial:

```text
users

boards
board_columns

task_categories
tasks
task_events
task_event_rules

levels
xp_transactions

achievements
user_achievements

titles
user_titles

daily_checkins

challenges
user_challenges
```

## 45. Seeders

Configurações iniciais não ficarão dentro das migrations.

Teremos:

```text
DatabaseSeeder
│
├── UserSeeder
├── LevelSeeder
├── BoardSeeder
├── BoardColumnSeeder
├── TaskCategorySeeder
├── TaskEventRuleSeeder
├── AchievementSeeder
├── TitleSeeder
└── ChallengeSeeder
```

## 46. Livewire Components

A aplicação poderá ser organizada assim:

```text
app/Livewire/

Dashboard/
    Index.php

Board/
    Index.php
    Show.php
    Create.php
    Edit.php

Task/
    Create.php
    Edit.php
    Show.php
    Card.php
    Kanban.php

Gamification/
    XpHistory.php
    LevelProgress.php
    Ranking.php
    Achievements.php
    Titles.php
    Challenges.php

Checkin/
    Button.php
    History.php

Admin/
    Users.php
    Categories.php
    EventRules.php
    Achievements.php
    Titles.php
    Challenges.php
```

Livewire suporta componentes aninhados, o que será útil para separar partes independentes do dashboard e do Kanban.

## 47. Blade Components

Nem tudo será Livewire.

Componentes puramente visuais:

```text
components/
├── badge
├── button
├── card
├── modal
├── avatar
├── progress-bar
├── xp-badge
├── level-badge
├── empty-state
├── dropdown
└── toast
```

Isso evita transformar elementos simples em componentes Livewire desnecessariamente.

## 48. Rotas

Como não haverá frontend separado, as rotas serão principalmente:

```text
routes/web.php
```

Exemplo:

```text
/login

/dashboard

/boards
/boards/{board}

/tasks/{task}

/ranking

/achievements
/titles
/challenges
/checkin

/admin/users
/admin/categories
/admin/achievements
/admin/titles
/admin/challenges
```

O Livewire pode atuar diretamente como página, além de ser incorporado dentro de views Blade.

## 49. Autorização

Será utilizado:

```text
Middleware
Policies
Gates
```

Exemplo:

```text
Admin
 ↓
Admin Policies

Developer
 ↓
Task Policies
```

Um Developer nunca poderá simplesmente manipular uma requisição para executar uma ação administrativa.

A autorização deverá ser feita no backend.

## 50. Formulários

Formulários Livewire deverão utilizar validação Laravel.

Exemplo:

```text
TaskCreate
TaskEdit
BoardCreate
CategoryCreate
ChallengeCreate
```

A validação deverá acontecer no componente e, quando a regra for mais complexa, ser delegada a uma classe de domínio/service.

## 51. Notificações

O sistema deverá possuir notificações visuais.

Exemplo:

```text
🎉 LEVEL UP!

Você alcançou o Level 13.
```

```text
🏆 Achievement unlocked!

Você desbloqueou:
Bug Hunter
```

```text
🔥 Streak!

Você completou 5 dias consecutivos.
+5 XP
```

Inicialmente podem ser notificações de interface.

Posteriormente:

```text
database notifications
email
```

## 52. Gamificação visual

O sistema deverá utilizar elementos visuais para tornar a experiência interessante.

Exemplos:

* barras de progresso;
* badges;
* medalhas;
* níveis;
* animações;
* ranking;
* streak;
* cards de conquista;
* XP ganho;
* level up;
* feedback visual.

O objetivo é que o usuário perceba a progressão.

## 53. Anti-farming

O sistema deverá evitar que o usuário consiga gerar XP artificialmente.

Exemplo proibido:

```text
DOING
 ↓
REVIEW
 ↓
DOING
 ↓
REVIEW
 ↓
DOING
 ↓
REVIEW
```

Isso não poderá gerar XP infinitamente.

Eventos deverão possuir regras de execução.

## 54. Auditoria

Toda concessão de XP deverá responder:

```text
Quem recebeu?
Quanto recebeu?
Por quê?
Quando?
Qual tarefa?
Qual evento?
Qual regra?
```

Exemplo:

```text
Matheus

+20 XP

Motivo:
Deploy

Task:
#125

Data:
25/08/2026 16:45
```

## 55. Performance

O sistema deverá evitar:

* N+1 queries;
* consultas desnecessárias;
* carregamento de relacionamentos que não serão utilizados;
* `SELECT *` quando não necessário;
* processamento pesado em cada renderização Livewire.

No dashboard, dados independentes poderão ser componentes Livewire separados quando fizer sentido. O Livewire possui mecanismos de isolamento/lazy loading que podem ser usados para regiões mais pesadas da página.

## 56. Testes

O projeto deverá possuir testes para as regras mais importantes.

### Unit

Testar:

```text
Cálculo de XP
Cálculo de level
Streak
Achievement rules
Challenge rules
Priority multiplier
```

Exemplo:

```text
Bug + Critical

10 × 5 = 50 XP
```

### Feature

Testar:

```text
Login
Criação de tarefa
Atribuição
Movimentação
Conclusão
XP
Achievement
Check-in
Ranking
```

## 57. Fluxo completo da tarefa

O fluxo principal será:

```text
ADMIN
   │
   ▼
Criar Task
   │
   ▼
BACKLOG
   │
   ▼
TODO
   │
   ▼
DEVELOPER inicia
   │
   ├── TaskEvent
   │
   ▼
DOING
   │
   ▼
DEVELOPMENT_COMPLETED
   │
   └── +XP
   │
   ▼
REVIEW
   │
   └── +XP
   │
   ▼
TESTING
   │
   └── +XP
   │
   ▼
DONE
   │
   └── +XP
   │
   ├── Achievement?
   ├── Challenge?
   ├── Level Up?
   └── Ranking
```

## 58. Fluxo de gamificação

```text
AÇÃO DO USUÁRIO
       │
       ▼
Livewire
       │
       ▼
Service
       │
       ▼
Event
       │
       ├──────────────┐
       ▼              ▼
XP Service       Challenge
       │
       ▼
XP Transaction
       │
       ▼
Level Service
       │
       ├── Level Up
       │
       ▼
Achievement Service
       │
       ▼
Interface Livewire
```

## 59. MVP

A primeira versão deverá conter:

### Autenticação

* Login
* Logout
* Usuário
* Roles

### Kanban

* Boards
* Columns
* Tasks
* Categories
* Priorities
* Assignments
* Drag and drop
* Task events

### Gamificação

* XP
* XP por evento
* Levels 1–50
* Ranking
* Achievements
* Titles
* Challenges
* Check-in
* Streak

### Interface

* Dashboard
* Sidebar
* Navbar
* Kanban
* Perfil
* Ranking
* Conquistas
* Títulos
* Desafios
* Administração

## 60. Fora do MVP

Não serão implementados inicialmente:

```text
Integração GitHub
Integração GitLab
Integração Jira
API pública
Aplicativo mobile
Multi-tenant
Times complexos
Moedas virtuais
Loja
Recompensas financeiras
Integração Slack
Integração Discord
```

Mas a arquitetura deverá permitir essas extensões posteriormente.

## 61. Roadmap de desenvolvimento

Eu dividiria o desenvolvimento em fases.

### Fase 1 — Fundação

```text
Laravel 13
MySQL
Blade
Livewire
Tailwind
Autenticação
Layout
Roles
Policies
```

### Fase 2 — Kanban

```text
Boards
Columns
Categories
Tasks
Task assignment
Task status
Drag & Drop
```

### Fase 3 — Gamificação

```text
XP
XP Transactions
Task Events
Levels
Ranking
```

### Fase 4 — Progressão

```text
Achievements
Titles
Challenges
Check-in
Streak
```

### Fase 5 — UX

```text
Dashboard
Animations
Notifications
Progress bars
Ranking visual
Achievement cards
Level Up
```

### Fase 6 — Qualidade

```text
Feature tests
Unit tests
Policies
Performance
N+1 analysis
Refactoring
Code quality
```

## 62. Estrutura final esperada

Ao final, o projeto deverá se parecer conceitualmente com:

```text
                         DEV GAMIFY
                              │
             ┌────────────────┴────────────────┐
             │                                 │
          KANBAN                          GAMIFICAÇÃO
             │                                 │
      ┌──────┼──────┐              ┌───────────┼───────────┐
      │      │      │              │           │           │
    Board  Tasks  Events           XP        Levels      Ranking
             │                                 │
       Categories                         ┌─────┴─────┐
       Priorities                         │           │
       Assignment                    Achievements   Titles
                                                     │
                                                Challenges
                                                     │
                                                  Check-in
```

E tecnicamente:

```text
                    Laravel 13
                        │
              ┌─────────┴─────────┐
              │                   │
           Blade              Livewire
              │                   │
              └─────────┬─────────┘
                        │
                     Services
                        │
                 Events / Rules
                        │
                     Models
                        │
                     Eloquent
                        │
                      MySQL
```

## Decisão arquitetural definitiva

Para esse projeto, o stack está fechado em:

**Laravel 13 + Blade + Livewire + Alpine.js + Tailwind + MySQL, sem API separada e sem SPA.**

Isso também deixa o projeto mais interessante para o objetivo: o backend e o frontend passam a ser um único domínio, explorando de fato o modelo reativo do Livewire — inclusive componentes de página, componentes aninhados e atualização parcial da interface.
