<?php

namespace App\Models;

use App\Enums\TaskPriority;
use Database\Factories\TaskPriorityRuleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property TaskPriority $priority
 * @property string $multiplier
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['priority', 'multiplier'])]
class TaskPriorityRule extends Model
{
    /** @use HasFactory<TaskPriorityRuleFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'priority' => TaskPriority::class,
            'multiplier' => 'decimal:2',
        ];
    }

    /**
     * The admin-editable multiplier for a priority, falling back to the
     * enum's built-in default when no override row exists yet.
     */
    public static function multiplierFor(TaskPriority $priority): float
    {
        $override = static::where('priority', $priority)->value('multiplier');

        return $override !== null ? (float) $override : $priority->multiplier();
    }
}
