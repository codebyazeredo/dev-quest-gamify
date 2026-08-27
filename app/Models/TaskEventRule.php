<?php

namespace App\Models;

use App\Enums\TaskEventType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'xp_reward', 'active'])]
class TaskEventRule extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => TaskEventType::class,
            'active' => 'boolean',
        ];
    }
}
