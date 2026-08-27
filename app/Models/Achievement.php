<?php

namespace App\Models;

use App\Enums\AchievementConditionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'slug', 'description', 'icon', 'condition_type', 'condition_value', 'xp_reward', 'active'])]
class Achievement extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'condition_type' => AchievementConditionType::class,
            'active' => 'boolean',
        ];
    }

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function title(): HasOne
    {
        return $this->hasOne(Title::class);
    }
}
