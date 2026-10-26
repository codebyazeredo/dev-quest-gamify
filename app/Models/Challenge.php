<?php

namespace App\Models;

use App\Enums\ChallengeType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'icon', 'type', 'target', 'xp_reward', 'starts_at', 'ends_at', 'active'])]
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

    public function titles(): HasMany
    {
        return $this->hasMany(Title::class);
    }

    public function statusLabel(): string
    {
        return match (true) {
            ! $this->active => 'Inativo',
            $this->starts_at->isFuture() => 'Agendado',
            $this->ends_at->isPast() => 'Encerrado',
            default => 'Em andamento',
        };
    }

    public function statusColor(): string
    {
        return match ($this->statusLabel()) {
            'Em andamento' => 'success',
            'Agendado' => 'info',
            'Encerrado' => 'secondary',
            default => 'secondary',
        };
    }
}
