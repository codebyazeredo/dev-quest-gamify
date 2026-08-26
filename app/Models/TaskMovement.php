<?php

namespace App\Models;

use Database\Factories\TaskMovementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $task_id
 * @property int|null $from_column_id
 * @property int $to_column_id
 * @property int $user_id
 * @property string|null $note
 * @property Carbon|null $created_at
 */
#[Fillable(['task_id', 'from_column_id', 'to_column_id', 'user_id', 'note', 'created_at'])]
class TaskMovement extends Model
{
    /** @use HasFactory<TaskMovementFactory> */
    use HasFactory;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Task, $this>
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * @return BelongsTo<BoardColumn, $this>
     */
    public function fromColumn(): BelongsTo
    {
        return $this->belongsTo(BoardColumn::class, 'from_column_id');
    }

    /**
     * @return BelongsTo<BoardColumn, $this>
     */
    public function toColumn(): BelongsTo
    {
        return $this->belongsTo(BoardColumn::class, 'to_column_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
