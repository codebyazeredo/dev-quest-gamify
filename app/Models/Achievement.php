<?php

namespace App\Models;

use App\Enums\AchievementConditionType;
use Database\Factories\AchievementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon
 * @property AchievementConditionType $condition_type
 * @property int $condition_value
 * @property int $xp_reward
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'description', 'icon', 'condition_type', 'condition_value', 'xp_reward', 'active'])]
class Achievement extends Model
{
    /** @use HasFactory<AchievementFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'condition_type' => AchievementConditionType::class,
            'active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<UserAchievement, $this>
     */
    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    /**
     * @return HasOne<Title, $this>
     */
    public function title(): HasOne
    {
        return $this->hasOne(Title::class);
    }
}
