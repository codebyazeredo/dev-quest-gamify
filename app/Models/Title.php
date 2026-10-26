<?php

namespace App\Models;

use App\Enums\TitleUnlockType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'icon', 'unlock_type', 'achievement_id', 'challenge_id', 'challenges_required', 'active'])]
class Title extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'unlock_type' => TitleUnlockType::class,
        ];
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function userTitles(): HasMany
    {
        return $this->hasMany(UserTitle::class);
    }

    public function unlockRuleDescription(): string
    {
        return match ($this->unlock_type) {
            TitleUnlockType::CHALLENGE => 'Concluir o desafio "'.($this->challenge?->name ?? 'removido').'"',
            TitleUnlockType::CHALLENGES_COUNT => 'Concluir '.$this->challenges_required.' '.($this->challenges_required === 1 ? 'desafio' : 'desafios'),
            default => $this->achievement
                ? 'Desbloquear a conquista "'.$this->achievement->name.'"'
                : 'Concedido manualmente',
        };
    }
}
