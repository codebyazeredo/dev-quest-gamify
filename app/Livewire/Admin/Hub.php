<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\RequiresAdminAccess;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Hub extends Component
{
    use RequiresAdminAccess;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function render(): View
    {
        $sections = [
            [
                'label' => 'Gestão',
                'items' => [
                    ['route' => 'admin.users', 'label' => 'Usuários', 'description' => 'Contas de acesso ao sistema.', 'icon' => 'users'],
                    ['route' => 'admin.people', 'label' => 'Pessoas', 'description' => 'Dados cadastrais da equipe.', 'icon' => 'id-card'],
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

        return view('livewire.admin.hub', ['sections' => $sections]);
    }
}
