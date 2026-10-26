<?php

namespace App\Livewire\Concerns;

use App\Enums\TitleUnlockType;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

trait ValidatesTitleUnlock
{
    protected function unlockRules(): array
    {
        return [
            'description' => ['nullable', 'string', 'max:255'],
            'unlock_type' => ['required', new Enum(TitleUnlockType::class)],
            'achievement_id' => ['nullable', 'exists:achievements,id'],
            'challenge_id' => [
                Rule::requiredIf($this->unlock_type === TitleUnlockType::CHALLENGE->value),
                'nullable',
                'exists:challenges,id',
            ],
            'challenges_required' => [
                Rule::requiredIf($this->unlock_type === TitleUnlockType::CHALLENGES_COUNT->value),
                'nullable',
                'integer',
                'min:1',
                'max:1000',
            ],
        ];
    }

    protected function unlockMessages(): array
    {
        return [
            'unlock_type.required' => 'Escolha como o título é desbloqueado.',
            'challenge_id.required' => 'Escolha o desafio que concede este título.',
            'challenge_id.exists' => 'O desafio escolhido não existe mais.',
            'challenges_required.required' => 'Informe quantos desafios precisam ser concluídos.',
            'challenges_required.min' => 'A quantidade mínima de desafios é 1.',
            'challenges_required.max' => 'A quantidade máxima de desafios é 1000.',
        ];
    }
}
