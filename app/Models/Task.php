<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Carbon\CarbonInterface;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $board_id
 * @property int $column_id
 * @property int $category_id
 * @property int|null $assigned_to
 * @property int $created_by
 * @property string $title
 * @property string|null $description
 * @property TaskPriority $priority
 * @property TaskStatus $status
 * @property int $position
 * @property int $base_points
 * @property string $priority_multiplier
 * @property CarbonInterface|null $due_at
 * @property string|null $rejection_reason
 * @property CarbonInterface|null $rejected_at
 * @property int|null $approved_by
 * @property CarbonInterface|null $started_at
 * @property CarbonInterface|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'board_id', 'column_id', 'category_id', 'assigned_to', 'created_by',
    'title', 'description', 'priority', 'status', 'position',
    'base_points', 'priority_multiplier', 'due_at',
    'rejection_reason', 'rejected_at', 'approved_by',
    'started_at', 'completed_at',
])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'priority' => TaskPriority::class,
            'status' => TaskStatus::class,
            'priority_multiplier' => 'decimal:2',
            'due_at' => 'datetime',
            'rejected_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Board, $this>
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * @return BelongsTo<BoardColumn, $this>
     */
    public function column(): BelongsTo
    {
        return $this->belongsTo(BoardColumn::class, 'column_id');
    }

    /**
     * @return BelongsTo<TaskCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class, 'category_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasMany<TaskEvent, $this>
     */
    public function taskEvents(): HasMany
    {
        return $this->hasMany(TaskEvent::class)->orderBy('occurred_at');
    }

    /**
     * @return HasMany<TaskMovement, $this>
     */
    public function movements(): HasMany
    {
        return $this->hasMany(TaskMovement::class)->orderBy('created_at');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * The one-time completion XP award: base category points scaled by the frozen priority multiplier.
     */
    public function xpValue(): int
    {
        return (int) round($this->base_points * (float) $this->priority_multiplier);
    }

    /**
     * A task delivered after its deadline earns zero XP (see TaskService).
     */
    public function isLate(): bool
    {
        return $this->completed_at !== null
            && $this->due_at !== null
            && $this->completed_at->gt($this->due_at);
    }
}
