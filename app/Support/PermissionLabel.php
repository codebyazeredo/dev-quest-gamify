<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * PT-BR display labels for permission names seeded by RoleSeeder. Falls back
 * to a title-cased version of the raw name for any future/custom permission
 * an admin creates via /admin/roles, so the UI never shows a blank label.
 */
class PermissionLabel
{
    public static function for(string $name): string
    {
        return match ($name) {
            'create-task' => 'Criar tarefa',
            'move-task' => 'Mover tarefa',
            'test-task' => 'Testar tarefa',
            'manage-board' => 'Gerenciar board',
            'assign-task' => 'Atribuir tarefa',
            'manage-users' => 'Gerenciar usuários',
            'manage-people' => 'Gerenciar pessoas',
            'manage-admin-settings' => 'Configurações administrativas',
            default => Str::headline($name),
        };
    }
}
