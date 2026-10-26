<?php

namespace App\Http\Requests\Admin;

use App\Enums\ChallengeType;
use App\Http\Requests\Concerns\ValidatesRewardTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Valida criação e edição de desafio, incluindo o título de recompensa opcional. */
class ChallengeRequest extends FormRequest
{
    use ValidatesRewardTitle;

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', Rule::unique('challenges', 'name')->ignore($this->route('challenge'))],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:20'],
            'type' => ['required', 'integer', Rule::enum(ChallengeType::class)],
            'target' => ['required', 'integer', 'min:1'],
            'xp_reward' => ['required', 'integer', 'min:0'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'active' => $this->isMethod('POST') ? ['boolean'] : ['required', 'boolean'],
            ...$this->rewardTitleRules(),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'ends_at.after' => 'O fim do desafio deve ser depois do início.',
            'type.enum' => 'Escolha um tipo de desafio válido.',
            ...$this->rewardTitleMessages(),
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'description' => 'descrição',
            'icon' => 'ícone',
            'type' => 'tipo',
            'target' => 'meta',
            'xp_reward' => 'XP',
            'starts_at' => 'início',
            'ends_at' => 'fim',
        ];
    }
}
