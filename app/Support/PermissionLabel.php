<?php

namespace App\Support;

use Illuminate\Support\Str;

class PermissionLabel
{
    public static function for(string $name): string
    {
        return match ($name) {
            'create-task' => 'Criar tarefa',
            'move-task' => 'Mover tarefa',
            'test-task' => 'Testar tarefa',
            'manage-board' => 'Gerenciar quadros',
            'assign-task' => 'Atribuir tarefa',
            'manage-users' => 'Gerenciar usuários',
            'manage-people' => 'Gerenciar pessoas',
            'manage-admin-settings' => 'Configurações administrativas',
            default => Str::headline($name),
        };
    }
}
