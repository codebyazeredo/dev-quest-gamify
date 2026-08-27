<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'base_points', 'color', 'text_color'])]
class TaskCategory extends Model
{
    use HasFactory;

    public const COLOR_PRESETS = [
        ['bg' => '#dbeafe', 'text' => '#1e3a8a', 'label' => 'Azul claro'],
        ['bg' => '#1e3a8a', 'text' => '#dbeafe', 'label' => 'Azul escuro'],
        ['bg' => '#dcfce7', 'text' => '#14532d', 'label' => 'Verde claro'],
        ['bg' => '#14532d', 'text' => '#dcfce7', 'label' => 'Verde escuro'],
        ['bg' => '#fee2e2', 'text' => '#7f1d1d', 'label' => 'Vermelho claro'],
        ['bg' => '#7f1d1d', 'text' => '#fee2e2', 'label' => 'Vermelho escuro'],
        ['bg' => '#fef3c7', 'text' => '#78350f', 'label' => 'Amarelo claro'],
        ['bg' => '#78350f', 'text' => '#fef3c7', 'label' => 'Marrom'],
        ['bg' => '#ede9fe', 'text' => '#4c1d95', 'label' => 'Roxo claro'],
        ['bg' => '#4c1d95', 'text' => '#ede9fe', 'label' => 'Roxo escuro'],
        ['bg' => '#fce7f3', 'text' => '#831843', 'label' => 'Rosa claro'],
        ['bg' => '#cffafe', 'text' => '#164e63', 'label' => 'Ciano claro'],
        ['bg' => '#164e63', 'text' => '#cffafe', 'label' => 'Ciano escuro'],
        ['bg' => '#e2e8f0', 'text' => '#1e293b', 'label' => 'Cinza claro'],
        ['bg' => '#1e293b', 'text' => '#e2e8f0', 'label' => 'Cinza escuro'],
        ['bg' => '#1c1917', 'text' => '#f5f5f4', 'label' => 'Preto'],
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'category_id');
    }
}
