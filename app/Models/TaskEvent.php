<?php

namespace App\Models;

use App\Enums\TaskEventType;
use App\Enums\XpSourceType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['task_id', 'type', 'user_id', 'occurred_at'])]
class TaskEvent extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'type' => TaskEventType::class,
            'occurred_at' => 'datetime',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function xpTransaction(): HasOne
    {
        return $this->hasOne(XpTransaction::class, 'source_id')
            ->where('source_type', XpSourceType::TASK_EVENT);
    }
}
