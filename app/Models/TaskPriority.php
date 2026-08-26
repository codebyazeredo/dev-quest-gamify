<?php

namespace App\Models;

use Database\Factories\TaskPriorityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $multiplier
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'multiplier'])]
class TaskPriority extends Model
{
    /** @use HasFactory<TaskPriorityFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'multiplier' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'priority_id');
    }
}
