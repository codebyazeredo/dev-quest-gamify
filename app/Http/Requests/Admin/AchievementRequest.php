<?php

namespace App\Http\Requests\Admin;

use App\Enums\AchievementConditionType;
use App\Http\Requests\Concerns\ValidatesRewardTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Valida criação e edição de conquista, incluindo o título de recompensa opcional. */
class AchievementRequest extends FormRequest
{
    use ValidatesRewardTitle;

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', Rule::unique('achievements', 'name')->ignore($this->route('achievement'))],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:10'],
            'condition_type' => ['required', 'integer', Rule::enum(AchievementConditionType::class)],
            'condition_value' => ['required', 'integer', 'min:1'],
            'xp_reward' => ['required', 'integer', 'min:0'],
            'active' => $this->isMethod('POST') ? ['boolean'] : ['required', 'boolean'],
            ...$this->rewardTitleRules(),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return $this->rewardTitleMessages();
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'description' => 'descrição',
            'icon' => 'ícone',
            'condition_type' => 'condição',
            'condition_value' => 'valor da condição',
            'xp_reward' => 'XP',
        ];
    }
}
