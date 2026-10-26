# Planning — Dev Quest Gamify

Levantamento de melhorias no que já existe e de novas funcionalidades, feito após a migração para Bootstrap 5.3 e a nova identidade visual (estilo Jira). Os itens estão priorizados para o workshop no SENAI e para a evolução depois dele.

Legenda de esforço: **P** (horas), **M** (1–2 dias), **G** (vários dias). Prioridade: **Alta**, **Média**, **Baixa**.

## Status da implementação (26/10/2026)

**Implementado (sem build/testes executados ainda):**

- **Kanban:** indicador de destino ao arrastar (o card some da origem e um espaço pontilhado mostra onde ele cai, com os cards da coluna abrindo espaço), rolagem automática nas bordas, atualização otimista; filtros por busca, categoria, prioridade e "Minhas tarefas"; contador de cada coluna respeita os filtros (#1, #2); mais cor primária (K1–K4: acento nas colunas, contador, cards "meus" e destino do arraste).
- **Modo noturno** (N1, N3–N5): seletor Claro/Escuro/Auto no menu lateral, tema aplicado antes do CSS, derivados da cor primária para o escuro.
- **Painel administrativo** (#53) em `/admin/dashboard` e **Minha conta** com tarefas em aberto e prazos (#58).
- **Desafios:** novos tipos (deploys, check-ins), ícone, atalhos de período, pré-visualização, duplicar para o próximo período, ativar/desativar na lista, filtros, participação (concluíram/participantes) e título de recompensa criado junto.
- **Conquistas:** novas condições (desafios concluídos, check-ins), duplicar, ativar/desativar, filtros, pré-visualização e título criado junto.
- **Títulos:** regra de desbloqueio por conquista, por **desafio específico** ou por **quantidade de desafios concluídos**; concessão retroativa a quem já cumpriu; evento/notificação de título desbloqueado; lista do jogador mostra títulos bloqueados com a regra e o progresso.

**Ainda pendente do plano:** #5 arrastar no toque, #19 mover consultas dos componentes para repositórios, #18 cache do `AppSetting`, N2 tema por usuário, N6–N10 (revisão fina do escuro), K5–K9, #25 documentação.

---

## 1. Antes do workshop (checklist)

- [ ] Rodar `php artisan migrate` (coluna `app_settings.primary_color`).
- [ ] Rodar `npm install` e `npm run build`; conferir login, quadro, dashboard, ranking, check-in e Configurações no navegador (desktop e celular).
- [ ] Rodar `php artisan test`, `composer lint:check` e `composer types:check` (nenhuma verificação foi executada após o redesign).
- [ ] Testar a troca da cor primária com cores claras (amarelo, turquesa) e escuras (grafite) para validar o contraste do texto.
- [ ] Testar o arrastar e soltar do Kanban com os cards novos e os filtros ativos.
- [ ] Popular dados de demonstração (`DemoDataSeeder`) e ensaiar o fluxo completo: criar tarefa → assumir → mover → aprovar → ver XP, nível e ranking.
- [ ] Preparar um usuário por role (admin, PO, dev, tester, suporte) para mostrar as diferenças de permissão.

---

## 2. Melhorias no que já existe

### 2.1 Interface e experiência

| # | Item | Prioridade | Esforço |
|---|------|-----------|---------|
| 1 | **Contagem por coluna reflete o filtro.** Hoje a busca e "Minhas tarefas" escondem cards, mas o número no cabeçalho da coluna continua o total. | Média | P |
| 2 | **Mais filtros no quadro:** categoria, prioridade e responsável (multi-seleção), além da busca e de "Minhas tarefas". Persistir os filtros por quadro (query string ou `localStorage`). | Alta | M |
| 3 | **Estado de carregamento e feedback.** Usar `wire:loading` em ações do Kanban (mover, assumir) e `wire:target` nos botões de salvar, evitando duplo clique. | Média | P |
| 4 | **Atualização otimista no arrastar.** O card só "assenta" depois da resposta do servidor; mover primeiro no cliente e reverter em caso de erro deixa o quadro mais fluido. | Média | M |
| 5 | **Arrastar em dispositivos de toque.** O drag and drop usa a API HTML5 nativa, que não funciona bem no celular/tablet. Avaliar uma lib pequena (SortableJS) ou um botão "Mover para…" no card. | Alta | M |
| 6 | **Detalhe da tarefa em painel lateral (offcanvas)** abrindo sobre o quadro, em vez de trocar de página, como no Jira. Manter a página completa para link direto. | Média | M |
| 7 | **Limite de altura e "mostrar mais" nas colunas** muito cheias, e indicador de limite WIP opcional por coluna. | Baixa | M |
| 8 | **Modo escuro** — detalhado na seção 2.5 (itens N1–N10). | Média | M |
| 9 | **Estados vazios ilustrados** em quadros, ranking, desafios e histórico, com chamada para ação ("Criar primeira tarefa"). | Baixa | P |
| 10 | **Acessibilidade.** Navegação por teclado no Kanban (mover card com atalhos), foco visível nos modais, `aria-live` nos contadores, revisão de contraste das cores de categoria. | Média | M |
| 11 | **Tabelas administrativas:** busca, ordenação por coluna e filtros (hoje só paginação). | Média | M |
| 12 | **Logo e favicon.** Usar a logo configurada também como favicon e na tela de login responsiva; validar proporção (a logo atual é horizontal e fica pequena no quadrado da barra fina). | Baixa | P |
| 13 | **Breadcrumb completo.** Hoje mostra só "Voltar / Título"; incluir o nível intermediário (ex.: Quadros / Quadro / Tarefa). | Baixa | P |
| 14 | **Internacionalização.** Os textos estão fixos em português nas views; mover para `lang/` caso o produto venha a ter outros idiomas. | Baixa | G |

### 2.2 Identidade visual configurável

| # | Item | Prioridade | Esforço |
|---|------|-----------|---------|
| 15 | **Mais personalização por cliente:** cor da barra fina e do menu lateral (hoje só a primária), cor de destaque secundária e fonte. | Média | M |
| 16 | **Validação de contraste na tela de configurações:** avisar quando a cor escolhida tiver pouco contraste com branco/preto (WCAG AA). | Média | P |
| 17 | **Pré-visualização do login** na tela de configurações (logo + cor). | Baixa | P |
| 18 | **Cache do `AppSetting::current()`.** É consultado em toda requisição pelo layout; cachear (ex.: `Cache::rememberForever`) e invalidar ao salvar. | Média | P |

### 2.3 Código e arquitetura

| # | Item | Prioridade | Esforço |
|---|------|-----------|---------|
| 19 | **Consultas Eloquent diretas em componentes Livewire**, contrariando a regra do README (Livewire → Service → Repository → Model). Ocorrências: `Board/Archive`, `Board/Index`, `Dashboard/Index`, `Gamification/Ranking`, `Task/Create` e `Task/Show` (`User::with(...)`, `Task::where(...)`). Mover para repositórios/serviços. | Alta | M |
| 20 | **Cor primária: lógica de contraste na view.** O cálculo de luminância está em `components/theme-style.blade.php`; extrair para um método testável (ex.: `AppSetting::primaryContrastColor()`) com teste unitário. | Média | P |
| 21 | **Autorização repetida em views** (`auth()->user()->can(...)` dentro do card). Calcular as permissões no componente e passar prontas, reduzindo avaliações de policy por card. | Média | M |
| 22 | **Eager loading e N+1.** Revisar com `Model::preventLazyLoading()` em desenvolvimento e Laravel Debugbar/Telescope; já corrigidos Kanban, Arquivo e Ranking, mas Dashboard, Usuários e Pessoas merecem auditoria. | Média | P |
| 23 | **Testes de interface (Livewire):** cobrir a tela de Configurações (cor primária na página), o filtro do quadro e o render dos componentes novos (`x-avatar`, `x-data-table`, `x-row-actions`). | Média | M |
| 24 | **Teste de contraste/tema:** teste de feature garantindo que o layout injeta `--bs-primary` com a cor salva. | Baixa | P |
| 25 | **Documentação:** atualizar `docs/REQUISITOS.md` (seção Frontend/Layout ainda descreve navbar e Tailwind em vários pontos) e registrar o guia de componentes Blade. | Média | P |
| 26 | **Upload de logo/foto:** validar dimensões, converter para WebP, remover arquivos órfãos e limitar tamanho por política. | Baixa | M |

### 2.4 Segurança e operação

| # | Item | Prioridade | Esforço |
|---|------|-----------|---------|
| 27 | **Rate limit no login** e bloqueio progressivo (verificar o que o Fortify/Breeze atual já faz). | Média | P |
| 28 | **Política de senha** por ambiente (hoje só produção tem regras fortes) e opção de expirar sessões inativas. | Baixa | P |
| 29 | **Logs de erro com mensagens específicas** nas novas telas (regra do projeto: nada de "Erro!" genérico) — revisar ações Livewire existentes que ainda usam mensagens genéricas. | Média | P |
| 30 | **Backup e seeds de demonstração** documentados para reproduzir o ambiente do workshop. | Média | P |

### 2.5 Kanban: mais cor primária e modo noturno

Hoje o quadro é quase todo neutro (colunas cinza, cards brancos) e a cor primária só aparece em botões, no item ativo do menu e em detalhes. O objetivo é que a identidade escolhida pelo cliente seja percebida no quadro, e que o quadro funcione bem no escuro. Tudo deve derivar de `--bs-primary` (nada de cores fixas), para continuar respeitando a configuração.

#### Uso da cor primária

| # | Item | Prioridade | Esforço |
|---|------|-----------|---------|
| K1 | **Cabeçalho da coluna com acento da cor primária:** faixa superior de 3px (`border-top` em `--bs-primary`) na coluna e título em `--bs-primary-text-emphasis`. A coluna de destino do arrasto já usa a cor primária; estender para a coluna "ativa" (com foco/filtro). | Alta | P |
| K2 | **Card com realce primário no hover e no foco:** borda esquerda de 3px em `--bs-primary` ao passar o mouse/selecionar, e anel de foco (`--bs-focus-ring-color`) para navegação por teclado. | Alta | P |
| K3 | **"Minhas tarefas" destacadas:** cards atribuídos ao usuário logado com fundo `--bs-primary-bg-subtle` e avatar com anel primário, mesmo sem filtro ativo. | Alta | P |
| K4 | **Contador da coluna em pílula primária suave** (`bg-primary-subtle` + `text-primary-emphasis`) e destaque quando a coluna passar do limite WIP. | Média | P |
| K5 | **Barra de ferramentas do quadro** (busca, filtros) com o botão de filtro ativo em `btn-primary` e um chip por filtro aplicado, cada um com "×" para remover. | Média | P |
| K6 | **Barra de progresso por coluna/quadro** (tarefas concluídas ÷ total) em `bg-primary` no cabeçalho do quadro, estilo "épico" do Jira. | Média | M |
| K7 | **Etiqueta de categoria opcionalmente na cor primária** (variante "monocromática") para clientes que preferem um quadro mais sóbrio; escolha por quadro nas configurações. | Baixa | M |
| K8 | **Cabeçalho do quadro com faixa na cor primária** (título, breadcrumb e ações sobre `bg-primary-subtle`), reforçando a identidade logo no topo. | Média | P |
| K9 | **Validação de contraste:** a cor da categoria já vem com texto legível, mas o realce primário (K1–K4) precisa de teste com cores claras (amarelo/turquesa) e escuras (grafite). | Alta | P |

Critérios de aceite: com qualquer cor escolhida em Configurações, o quadro mostra pelo menos o acento nas colunas, o realce nos cards do usuário e o botão de filtro; nenhum texto fica abaixo de contraste 4.5:1.

#### Modo noturno

O Bootstrap 5.3 oferece `data-bs-theme="dark"`; o que falta é adotar o tema em todo o app e revisar as cores fixas do CSS.

| # | Item | Prioridade | Esforço |
|---|------|-----------|---------|
| N1 | **Seletor de tema** (claro / escuro / seguir o sistema) no menu do usuário, salvo em `localStorage` e aplicado em `<html data-bs-theme>` **antes** do carregamento do CSS (script inline no `<head>`) para evitar "flash" branco. | Alta | P |
| N2 | **Persistir a preferência por usuário** (coluna `theme` em `users` ou preferências) para valer em outros dispositivos; `localStorage` como fallback para a tela de login. | Média | M |
| N3 | **Trocar cores fixas por variáveis do Bootstrap** em `app.css`: `--bs-body-bg`, `--bs-body-color`, `--bs-secondary-bg`, `--bs-border-color` e `--bs-tertiary-color` são definidos só para o tema claro; criar o bloco `[data-bs-theme='dark']` com valores equivalentes (ex.: fundo `#161a1d`, cards `#22272b`, colunas `#1d2125`, texto `#b6c2cf`, como o Jira escuro). | Alta | M |
| N4 | **Elementos com cor fixa a revisar:** menu lateral (`#f7f8f9`), `.kanban-card` (sombra e `:hover`), `.kanban-column`, `.count-pill`, `.brand-tile` (fundo branco), `.auth-card`/`.auth-page`, hero do dashboard e `bg-body`/`bg-white` espalhados nas views (`bg-body-tertiary`, `btn-outline-secondary bg-body`). | Alta | M |
| N5 | **Derivados da cor primária no escuro:** `--bs-primary-bg-subtle` e `--bs-primary-text-emphasis` hoje misturam com branco/preto para o tema claro; no escuro devem inverter (fundo misturado com preto, texto misturado com branco). Ajustar `x-theme-style` para gerar os dois conjuntos. | Alta | M |
| N6 | **Etiquetas de categoria no escuro:** os pares claro/escuro de `TaskCategory::COLOR_PRESETS` foram pensados para fundo branco; garantir legibilidade (ex.: reduzir opacidade/saturação no escuro ou borda sutil) e manter a cor escolhida pelo admin. | Alta | M |
| N7 | **Logo no escuro:** permitir enviar uma segunda versão da logo (para fundo escuro) ou aplicar fundo claro no `brand-tile`; revisar a logo da tela de login. | Média | P |
| N8 | **Gráficos e imagens:** quando houver dashboards com gráficos (#53/#54), ler as cores das variáveis CSS para respeitar o tema. | Média | P |
| N9 | **Toasts, modais, dropdowns e tabelas** conferidos no escuro (o Bootstrap cobre a maior parte; validar `table-light`, `card-header bg-body`, `bg-*-subtle`). | Média | P |
| N10 | **Testes visuais:** conferir cada tela nos dois temas e com 3 cores primárias (azul, vermelho, amarelo); registrar capturas de tela de referência. | Média | M |

Critérios de aceite: troca de tema sem recarregar a página; nenhum bloco branco "vazado" no escuro; foco visível em ambos os temas; o Kanban continua usando a cor primária configurada nos dois modos.

Ordem sugerida: **N3 → N4 → N5 → N1** (base do tema), depois **K1–K3 + K9** (cor no quadro) e, por fim, **N6/N7** e os itens restantes.

---

## 3. Novas funcionalidades

Agrupadas por tema; a coluna "Base" indica o que já existe no projeto e facilita a implementação.

### 3.1 Gestão de tarefas (aproximar do Jira)

| # | Funcionalidade | Valor | Prioridade | Esforço | Base |
|---|---------------|-------|-----------|---------|------|
| 31 | **Comentários na tarefa** (com menção `@usuário`) | Colaboração | Alta | M | `TaskEvent` e página de detalhe |
| 32 | **Checklist / subtarefas** com progresso no card | Granularidade | Alta | M | `Task` |
| 33 | **Anexos** (imagens e arquivos) | Contexto | Média | M | Storage `public` já usado |
| 34 | **Etiquetas (labels)** livres, além da categoria | Organização | Média | M | `TaskCategory` |
| 35 | **Estimativa e tempo gasto** (pontos de história ou horas) | Planejamento | Média | M | Multiplicador de prioridade |
| 36 | **Sprints** (início/fim, meta, burndown simples) e **backlog** separado | Metodologia ágil | Média | G | `Board`, `BoardColumn` |
| 37 | **Dependências entre tarefas** (bloqueia / é bloqueada por) | Gestão de risco | Baixa | M | — |
| 38 | **Tarefas recorrentes** (ex.: rotina semanal) | Produtividade | Baixa | M | Scheduler Laravel |
| 39 | **Visões alternativas:** lista, tabela e calendário por prazo | Flexibilidade | Média | M | Query do quadro |
| 40 | **Busca global** (atalho `/`) por tarefas, pessoas e quadros, como na barra do Jira | Navegação | Média | M | Barra fina lateral |
| 41 | **Botão "Criar" global** (atalho `c`) abrindo o modal de nova tarefa de qualquer tela | Agilidade | Média | P | `Task\Create` |
| 42 | **Raias (swimlanes)** por responsável ou prioridade | Visualização | Baixa | G | Kanban |
| 43 | **Prazos com alertas:** destaque de "vence hoje/amanhã" e lembrete | Prazo | Média | P | `Task::isLate()` |

### 3.2 Gamificação

| # | Funcionalidade | Valor | Prioridade | Esforço | Base |
|---|---------------|-------|-----------|---------|------|
| 44 | **Perfil público do usuário** (nível, conquistas, títulos, histórico) acessível pelo ranking | Reconhecimento | Alta | M | Dashboard e `UserTitle` |
| 45 | **Ranking por equipe/quadro** e por período customizado | Competição saudável | Média | M | `Ranking` |
| 46 | **Temporadas** (reinício periódico do ranking com premiação/título de campeão) | Engajamento | Média | G | `XpTransaction` |
| 47 | **Loja de recompensas:** gastar XP em benefícios definidos pelo admin (avatar, tema, folga simbólica) | Retenção | Baixa | G | `XpTransaction` |
| 48 | **Reconhecimento entre pares** (enviar "kudos" com XP limitado por semana) | Cultura | Média | M | Eventos/Listeners |
| 49 | **Missões diárias/semanais automáticas**, além dos desafios cadastrados | Hábito | Média | M | `Challenge` |
| 50 | **Linha do tempo de conquistas** e compartilhamento (imagem/PDF do certificado) | Motivação | Baixa | M | `UserAchievement` |
| 51 | **Painel anti-farming:** relatório de padrões suspeitos (muitas movimentações, XP fora da curva) | Integridade | Média | M | Seção 53 dos requisitos |
| 52 | **Níveis com identidade visual** (cores/ícones por faixa de nível no avatar e no ranking) | Gamificação visual | Baixa | P | `Level` |

### 3.3 Dashboards e relatórios

| # | Funcionalidade | Valor | Prioridade | Esforço | Base |
|---|---------------|-------|-----------|---------|------|
| 53 | **Dashboard administrativo** previsto em `REQUISITOS.md §13`: tarefas por status/categoria/prioridade, XP distribuído, desenvolvedores ativos, evolução semanal | Gestão | **Alta** | M | Ainda não existe; é o maior ganho visual para o workshop |
| 54 | **Gráficos** (Chart.js ou barras com `progress` do Bootstrap) para evolução de XP e throughput | Visualização | Alta | M | `XpTransaction` |
| 55 | **Relatórios exportáveis** (CSV/PDF): tarefas por período, XP por pessoa | Prestação de contas | Média | M | Repositórios |
| 56 | **Velocidade e lead time** por quadro/sprint | Métricas ágeis | Baixa | G | `TaskMovement` |
| 57 | **Dashboard do PO:** entregas da semana, gargalos por coluna, tarefas paradas há N dias | Acompanhamento | Média | M | `TaskMovement` |
| 58 | **Dashboard "Minha conta" evoluído:** minhas tarefas abertas, próximos prazos e atividade recente da equipe | Dia a dia | Alta | M | `Dashboard\Index` |

### 3.4 Notificações e integrações

| # | Funcionalidade | Valor | Prioridade | Esforço | Base |
|---|---------------|-------|-----------|---------|------|
| 59 | **Central de notificações** (sino com contador e histórico), hoje só há toasts efêmeros | Engajamento | Alta | M | `ToastCollector` |
| 60 | **Notificações por e-mail** (atribuição, comentário, aprovação/reprovação) com preferências | Alcance | Média | M | Mailpit já configurado |
| 61 | **Atualização em tempo real** do quadro (Laravel Reverb/Echo) | Colaboração | Média | G | Livewire 4 |
| 62 | **Webhooks e integração com Git** (vincular PR/commit à tarefa, mover ao fazer merge) | Integração | Baixa | G | — |
| 63 | **API REST/Token** para integrações externas | Extensibilidade | Baixa | G | Policies existentes |
| 64 | **Notificação no Slack/Teams** de eventos relevantes (level up, deploy) | Cultura | Baixa | M | Eventos |

### 3.5 Administração e plataforma

| # | Funcionalidade | Valor | Prioridade | Esforço | Base |
|---|---------------|-------|-----------|---------|------|
| 65 | **Auditoria completa** (quem alterou o quê) com tela de consulta (`REQUISITOS.md §54`) | Governança | Média | M | `XpTransaction`, `TaskEvent` |
| 66 | **Multiempresa (multi-tenant)** com identidade visual por cliente (já há cor e logo configuráveis) | Produto SaaS | Média | G | `AppSetting` |
| 67 | **Importar/exportar** usuários e tarefas (CSV) | Onboarding | Média | M | Repositórios |
| 68 | **Autenticação em dois fatores** e login social (Google/Microsoft) | Segurança | Baixa | M | Auth atual |
| 69 | **Modelos de quadro** (template de colunas e categorias por tipo de projeto) | Agilidade | Média | P | `seedDefaultColumns` |
| 70 | **PWA** (instalável, ícone, cache básico) | Mobilidade | Baixa | M | Vite |

---

## 4. Roteiro sugerido

### Fase 1 — Pronto para o workshop (1–3 dias)
1. Checklist da seção 1.
2. **#53 Dashboard administrativo** e **#58 evolução do "Minha conta"** — maior impacto visual na demonstração.
3. **#2 filtros do quadro**, **#1 contagem por filtro**, **#5 arrastar no toque**.
3. **K1–K3 e K9:** mais cor primária no Kanban, com teste de contraste.
3. **N3 → N4 → N5 → N1:** base do modo noturno (variáveis, cores fixas, derivados da cor primária, seletor de tema).
4. **#59 central de notificações** (versão simples: tabela + sino).
5. **#25 documentação** e **#16 aviso de contraste**.

### Fase 2 — Consolidação técnica (1 semana)
1. **#19 mover consultas dos componentes para repositórios/serviços** e **#22 auditoria de N+1**.
2. **#23/#24 testes** das telas novas.
3. **#18 cache do `AppSetting`**, **#20 contraste testável**, **#27 rate limit no login**.

### Fase 3 — Colaboração (2–3 semanas)
1. **#31 comentários**, **#32 checklist**, **#33 anexos**, **#6 painel lateral da tarefa**.
2. **#40 busca global** e **#41 botão Criar**.
3. **#44 perfil público** e **#45 ranking por equipe**.

### Fase 4 — Produto (1–2 meses)
1. **#36 sprints**, **#39 visões alternativas**, **#55 relatórios**.
2. **#46 temporadas**, **#48 kudos**, **#49 missões**.
3. **#60/#61 e-mail e tempo real**, **#65 auditoria**, **#66 multiempresa**.

---

## 5. Riscos e observações

- **Qualidade não verificada:** o redesign foi feito sem rodar build, migrations ou testes. Tratar a Fase 1 como validação obrigatória antes de novas funcionalidades.
- **Regra de arquitetura:** novas funcionalidades devem seguir Livewire → Service → Repository → Model; os desvios listados em #19 não devem servir de exemplo.
- **Drag and drop nativo:** limita o uso em celular (#5); definir a solução antes de investir em mais recursos no quadro.
- **Escopo do workshop:** evitar iniciar itens "G" antes do evento; priorizar o que melhora a demonstração (dashboard, filtros, notificações, polimento visual).
- **Cores configuráveis:** qualquer componente novo deve usar variáveis/utilitários do Bootstrap (`bg-primary`, `text-primary-emphasis`, `--bs-primary`) e nunca cores fixas, para respeitar a cor escolhida pelo cliente.
