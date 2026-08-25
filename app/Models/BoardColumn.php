<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Database\Factories\BoardColumnFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $board_id
 * @property string $name
 * @property string $slug
 * @property int $position
 * @property bool $is_final
 * @property TaskStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['board_id', 'name', 'slug', 'position', 'is_final', 'status'])]
class BoardColumn extends Model
{
    /** @use HasFactory<BoardColumnFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_final' => 'boolean',
            'status' => TaskStatus::class,
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
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'column_id')->orderBy('position');
    }

    /**
     * Create the 6 standard columns (matching TaskStatus) for a newly created board.
     */
    public static function seedDefaultsFor(Board $board): void
    {
        foreach (TaskStatus::cases() as $position => $status) {
            $board->columns()->create([
                'name' => $status->label(),
                'slug' => Str::slug($status->label()),
                'position' => $position,
                'is_final' => $status === TaskStatus::DONE,
                'status' => $status,
            ]);
        }
    }
}
