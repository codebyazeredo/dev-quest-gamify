<?php

namespace App\Http\Requests\Admin;

use App\Enums\TitleUnlockType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TitleRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $unlockType = $this->input('unlock_type');

        return [
            'name' => ['required', 'string', 'max:60', Rule::unique('titles', 'name')->ignore($this->route('title'))],
            'icon' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string', 'max:255'],
            'unlock_type' => ['required', Rule::enum(TitleUnlockType::class)],
            'achievement_id' => ['nullable', 'exists:achievements,id'],
            'challenge_id' => [
                Rule::requiredIf($unlockType === TitleUnlockType::CHALLENGE->value),
                'nullable',
                'exists:challenges,id',
            ],
            'challenges_required' => [
                Rule::requiredIf($unlockType === TitleUnlockType::CHALLENGES_COUNT->value),
                'nullable',
                'integer',
                'min:1',
                'max:1000',
            ],
            'active' => $this->isMethod('POST') ? ['boolean'] : ['required', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
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

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['name' => 'nome', 'description' => 'descrição', 'icon' => 'ícone'];
    }
}
