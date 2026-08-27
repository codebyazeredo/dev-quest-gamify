<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['board_id', 'name', 'slug', 'position', 'is_final', 'status'])]
class BoardColumn extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_final' => 'boolean',
            'status' => TaskStatus::class,
        ];
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'column_id')->orderBy('position');
    }

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
