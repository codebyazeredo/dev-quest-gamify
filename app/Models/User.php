<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'person_id', 'selected_title_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $guard_name = 'web';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isProductOwner(): bool
    {
        return $this->hasRole('product_owner');
    }

    public function isDeveloper(): bool
    {
        return $this->hasRole('dev');
    }

    public function isTester(): bool
    {
        return $this->hasRole('tester');
    }

    public function isSuporte(): bool
    {
        return $this->hasRole('suporte');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function xpTransactions(): HasMany
    {
        return $this->hasMany(XpTransaction::class);
    }

    public function selectedTitle(): BelongsTo
    {
        return $this->belongsTo(Title::class, 'selected_title_id');
    }

    public function unlockedAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function unlockedTitles(): HasMany
    {
        return $this->hasMany(UserTitle::class);
    }

    public function dailyCheckins(): HasMany
    {
        return $this->hasMany(DailyCheckin::class);
    }

    public function userChallenges(): HasMany
    {
        return $this->hasMany(UserChallenge::class);
    }
}
