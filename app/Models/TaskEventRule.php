<?php

namespace App\Models;

use App\Enums\TaskEventType;
use Database\Factories\TaskEventRuleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property TaskEventType $type
 * @property int $xp_reward
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['type', 'xp_reward', 'active'])]
class TaskEventRule extends Model
{
    /** @use HasFactory<TaskEventRuleFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => TaskEventType::class,
            'active' => 'boolean',
        ];
    }
}
