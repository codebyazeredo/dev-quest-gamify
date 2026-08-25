<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property UserRole $role
 * @property int|null $selected_title_id
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role', 'selected_title_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isProductOwner(): bool
    {
        return $this->role === UserRole::PRODUCT_OWNER;
    }

    public function isDeveloper(): bool
    {
        return $this->role === UserRole::DEVELOPER;
    }

    /**
     * @return HasMany<XpTransaction, $this>
     */
    public function xpTransactions(): HasMany
    {
        return $this->hasMany(XpTransaction::class);
    }

    /**
     * @return BelongsTo<Title, $this>
     */
    public function selectedTitle(): BelongsTo
    {
        return $this->belongsTo(Title::class, 'selected_title_id');
    }

    /**
     * @return HasMany<UserAchievement, $this>
     */
    public function unlockedAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    /**
     * @return HasMany<UserTitle, $this>
     */
    public function unlockedTitles(): HasMany
    {
        return $this->hasMany(UserTitle::class);
    }

    /**
     * @return HasMany<DailyCheckin, $this>
     */
    public function dailyCheckins(): HasMany
    {
        return $this->hasMany(DailyCheckin::class);
    }

    /**
     * @return HasMany<UserChallenge, $this>
     */
    public function userChallenges(): HasMany
    {
        return $this->hasMany(UserChallenge::class);
    }
}
