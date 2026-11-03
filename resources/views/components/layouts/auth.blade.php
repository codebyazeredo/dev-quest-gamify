@props(['title' => null, 'portal' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $appSettings = \App\Models\AppSetting::current();
            $panel = $portal
                ? [
                    'headline' => 'Seu suporte sempre ao seu alcance.',
                    'sublines' => [
                        'Abra chamados com anexos e acompanhe cada resposta da equipe.',
                        'Veja o andamento da solicitação etapa por etapa.',
                        'Responda e reabra o chamado quando precisar de mais ajuda.',
                        'Todo o histórico de chamados da sua empresa em um só lugar.',
                    ],
                ]
                : [
                    'headline' => 'Planeje, entregue e reconheça resultados.',
                    'sublines' => [
                        'Organize as entregas em quadros e acompanhe o andamento em tempo real.',
                        'Reconheça resultados com pontos, níveis e conquistas.',
                        'Metas e desafios que mantêm o time engajado e alinhado.',
                        'Ranking e indicadores para decidir com mais clareza.',
                    ],
                    'columns' => ['A fazer', 'Em andamento', 'Concluído'],
                    'tag' => 'Bug',
                    'cardTitle' => 'Corrigir cálculo de XP',
                    'chip' => '50 XP',
                    'pop' => '+50 XP',
                    'levelBefore' => 'Nível 12',
                    'levelAfter' => 'Nível 13',
                    'levelCaption' => 'XP da semana',
                ];
            $sparks = [[-3.2, -2.6], [-1.6, -3.4], [0.2, -3.8], [1.9, -3.3], [3.3, -2.4], [-2.6, -0.6], [2.8, -0.4], [0.4, -2]];
        @endphp

        <title>{{ $title ?? $appSettings->displayName() }}</title>

        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/x-icon" href="/favicon.ico" sizes="any">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <x-theme-init />

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <x-theme-style :color="$appSettings->primaryColor()" />
    </head>
    <body class="auth-split">
        <aside class="quest-panel">
            <div class="quest-orb quest-orb--a" aria-hidden="true"></div>
            <div class="quest-orb quest-orb--b" aria-hidden="true"></div>

            <div class="quest-brand" style="--quest-logo-h: {{ $appSettings->logo_size->loginHeightRem() }}rem;">
                @if ($appSettings->logoUrl())
                    <span class="quest-logo"><img src="{{ $appSettings->logoUrl() }}" alt="{{ $appSettings->displayName() }}"></span>
                @else
                    <i class="bi bi-controller" aria-hidden="true"></i>
                    <span>{{ $appSettings->displayName() }}</span>
                @endif
            </div>

            <div class="quest-hero">
                <p class="quest-headline">{{ $panel['headline'] }}</p>

                <div class="quest-sublines" aria-hidden="true">
                    @foreach ($panel['sublines'] as $subline)
                        <p class="quest-subline" style="--i: {{ $loop->index }};">{{ $subline }}</p>
                    @endforeach
                </div>
            </div>

            <div class="quest-stage" aria-hidden="true">
                @if ($portal)
                    <div class="quest-ticket">
                        <div class="quest-ticket-head">
                            <strong class="quest-ticket-title">Meus chamados</strong>
                            <span class="quest-new"><i class="bi bi-plus-lg"></i> Novo chamado</span>
                        </div>

                        <div class="quest-row">
                            <span class="quest-row-title">#46 · Fatura consolidada mensal por centro de custo</span>
                            <span class="quest-status">
                                <span class="quest-lv quest-lv--before">Aberto</span>
                                <span class="quest-lv quest-lv--after">Resolvido</span>
                            </span>
                        </div>
                        <div class="quest-row">
                            <span class="quest-row-title">#20 · Frete por tonelada com arredondamento diferente do contrato</span>
                            <span class="quest-badge quest-badge--done">Resolvido</span>
                        </div>
                    </div>

                    <div class="quest-level quest-level--steps">
                        <div class="quest-level-head">
                            <span class="quest-level-title"><i class="bi bi-kanban"></i> Andamento da solicitação</span>
                        </div>
                        <div class="quest-bar"><div class="quest-bar-fill"></div></div>
                        <ol class="quest-stepper">
                            @foreach (['Backlog', 'A Fazer', 'Em Andamento', 'Em Revisão', 'Em Teste', 'Aprovado', 'Concluído'] as $stepName)
                                <li>{{ $stepName }}</li>
                            @endforeach
                        </ol>
                    </div>
                @else
                <div class="quest-board">
                    @foreach ($panel['columns'] as $columnTitle)
                        <div class="quest-col">
                            <span class="quest-col-title">{{ $columnTitle }}</span>
                            <span class="quest-slot @if ($loop->first) quest-slot--origin @endif"></span>
                            <span class="quest-ghost"></span>
                            <span class="quest-ghost quest-ghost--short"></span>
                        </div>
                    @endforeach

                    <div class="quest-card">
                        <span class="quest-tag">{{ $panel['tag'] }}</span>
                        <strong class="quest-card-title">{{ $panel['cardTitle'] }}</strong>
                        <span class="quest-card-foot">
                            <span class="quest-chip">{{ $panel['chip'] }}</span>
                            <i class="bi bi-check-circle-fill quest-check"></i>
                        </span>
                    </div>

                    <span class="quest-xp-pop">{{ $panel['pop'] }}</span>
                </div>

                <div class="quest-level">
                    <div class="quest-level-head">
                        <span class="quest-level-label">
                            <span class="quest-lv quest-lv--before">{{ $panel['levelBefore'] }}</span>
                            <span class="quest-lv quest-lv--after">{{ $panel['levelAfter'] }}</span>
                        </span>
                        <span>{{ $panel['levelCaption'] }}</span>
                    </div>
                    <div class="quest-bar"><div class="quest-bar-fill"></div></div>

                    @foreach ($sparks as [$x, $y])
                        <span class="quest-spark" style="--x: {{ $x }}rem; --y: {{ $y }}rem;"></span>
                    @endforeach
                </div>
                @endif
            </div>
        </aside>

        <main class="auth-main">
            <div class="auth-theme"><x-theme-toggle /></div>

            <div class="auth-form-wrap">
                @if (session('status'))
                    <div class="alert alert-success d-flex align-items-start gap-2" role="alert">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                {{ $slot }}
            </div>

            <footer class="auth-footer">
                <p class="auth-footer-product">
                    <strong>DevQuest Gamify</strong> &middot; gestão de tarefas gamificada para times de desenvolvimento
                </p>
                <p class="auth-footer-credit">
                    Desenvolvido por <span class="app-footer-brand">Code<strong>by</strong>azeredo<sup>&reg;</sup></span>
                    &middot; &copy; {{ now()->year }}
                </p>
            </footer>
        </main>
    </body>
</html>
