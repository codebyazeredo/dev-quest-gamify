<?php

namespace App\Support;

/** Seções e atalhos exibidos na página inicial das configurações administrativas. */
class AdminHubMenu
{
    /** @return array<int, array{label: string, items: array<int, array<string, string>>}> */
    public static function sections(): array
    {
        return [
            [
                'label' => 'Visão geral',
                'items' => [
                    ['route' => 'admin.dashboard', 'label' => 'Painel administrativo', 'description' => 'Indicadores de tarefas, XP e equipe.', 'icon' => 'columns'],
                ],
            ],
            [
                'label' => 'Gestão',
                'items' => [
                    ['route' => 'admin.users', 'label' => 'Usuários', 'description' => 'Contas de acesso ao sistema.', 'icon' => 'users'],
                    ['route' => 'admin.people', 'label' => 'Pessoas', 'description' => 'Dados cadastrais da equipe.', 'icon' => 'id-card'],
                    ['route' => 'admin.clients', 'label' => 'Clientes', 'description' => 'Empresas do portal de chamados.', 'icon' => 'id-card'],
                    ['route' => 'admin.roles', 'label' => 'Roles', 'description' => 'Papéis e permissões.', 'icon' => 'shield'],
                ],
            ],
            [
                'label' => 'Gamificação',
                'items' => [
                    ['route' => 'admin.categories', 'label' => 'Categorias', 'description' => 'Tipos de tarefa e cores.', 'icon' => 'tag'],
                    ['route' => 'admin.event-rules', 'label' => 'Regras de XP', 'description' => 'Pontuação por evento da tarefa.', 'icon' => 'bolt'],
                    ['route' => 'admin.priority-rules', 'label' => 'Gravidade', 'description' => 'Multiplicadores de prioridade.', 'icon' => 'alert'],
                    ['route' => 'admin.achievements', 'label' => 'Conquistas', 'description' => 'Condições e recompensas.', 'icon' => 'trophy'],
                    ['route' => 'admin.titles', 'label' => 'Títulos', 'description' => 'Títulos desbloqueáveis.', 'icon' => 'medal'],
                    ['route' => 'admin.challenges', 'label' => 'Desafios', 'description' => 'Metas por período.', 'icon' => 'flag'],
                ],
            ],
            [
                'label' => 'Sistema',
                'items' => [
                    ['route' => 'admin.settings', 'label' => 'Configurações', 'description' => 'Nome e logo da empresa.', 'icon' => 'gear'],
                ],
            ],
        ];
    }
}
