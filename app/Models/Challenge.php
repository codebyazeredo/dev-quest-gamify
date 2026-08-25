<?php

namespace App\Models;

use App\Enums\ChallengeType;
use Database\Factories\ChallengeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property ChallengeType $type
 * @property int $target
 * @property int $xp_reward
 * @property Carbon $starts_at
 * @property Carbon $ends_at
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'description', 'type', 'target', 'xp_reward', 'starts_at', 'ends_at', 'active'])]
class Challenge extends Model
{
    /** @use HasFactory<ChallengeFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => ChallengeType::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<UserChallenge, $this>
     */
    public function userChallenges(): HasMany
    {
        return $this->hasMany(UserChallenge::class);
    }
}
