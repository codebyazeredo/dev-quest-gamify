<?php

namespace App\Models;

use App\Enums\TaskEventType;
use App\Enums\XpSourceType;
use Carbon\CarbonInterface;
use Database\Factories\TaskEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $task_id
 * @property TaskEventType $type
 * @property int $user_id
 * @property CarbonInterface $occurred_at
 * @property Carbon|null $created_at
 */
#[Fillable(['task_id', 'type', 'user_id', 'occurred_at'])]
class TaskEvent extends Model
{
    /** @use HasFactory<TaskEventFactory> */
    use HasFactory;

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'type' => TaskEventType::class,
            'occurred_at' => 'datetime',
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
     * The user who triggered this event.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasOne<XpTransaction, $this>
     */
    public function xpTransaction(): HasOne
    {
        return $this->hasOne(XpTransaction::class, 'source_id')
            ->where('source_type', XpSourceType::TASK_EVENT);
    }
}
