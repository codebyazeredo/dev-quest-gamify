<?php

namespace App\Models;

use App\Enums\ChallengeType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'type', 'target', 'xp_reward', 'starts_at', 'ends_at', 'active'])]
class Challenge extends Model
{
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

    public function userChallenges(): HasMany
    {
        return $this->hasMany(UserChallenge::class);
    }
}
