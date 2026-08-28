<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'board_id', 'column_id', 'category_id', 'priority_id', 'assigned_to', 'created_by',
    'title', 'description', 'status', 'position',
    'base_points', 'priority_multiplier', 'due_at',
    'rejection_reason', 'rejected_at', 'approved_by',
    'started_at', 'completed_at', 'archived_at',
])]
class Task extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority_multiplier' => 'decimal:2',
            'due_at' => 'datetime',
            'rejected_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(BoardColumn::class, 'column_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class, 'category_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TaskPriority::class, 'priority_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function taskEvents(): HasMany
    {
        return $this->hasMany(TaskEvent::class)->orderBy('occurred_at');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(TaskMovement::class)->orderBy('created_at');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function xpValue(): int
    {
        return (int) round($this->base_points * (float) $this->priority_multiplier);
    }

    public function isLate(): bool
    {
        return $this->completed_at !== null
            && $this->due_at !== null
            && $this->completed_at->gt($this->due_at);
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if (! $user->isDeveloper()) {
            return $query;
        }

        return $query->where(function (Builder $visible) use ($user) {
            $visible->where(function (Builder $backlog) {
                $backlog->where('status', TaskStatus::BACKLOG)->whereNull('assigned_to');
            })
                ->orWhere('status', TaskStatus::REVIEW)
                ->orWhere(function (Builder $untaggedUnassigned) {
                    $untaggedUnassigned->whereNull('status')->whereNull('assigned_to');
                })
                ->orWhere(function (Builder $mine) use ($user) {
                    $mine->where(function (Builder $notBacklogOrReview) {
                        $notBacklogOrReview->whereNotIn('status', [TaskStatus::BACKLOG, TaskStatus::REVIEW])
                            ->orWhereNull('status');
                    })->where('assigned_to', $user->id);
                });
        });
    }
}
