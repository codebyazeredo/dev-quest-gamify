<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Rule;

trait ValidatesRewardTitle
{
    /** @return array<string, array<int, mixed>> */
    protected function rewardTitleRules(): array
    {
        return [
            'award_title' => ['boolean'],
            'reward_title_name' => [
                Rule::requiredIf($this->boolean('award_title')),
                'nullable',
                'string',
                'max:60',
                'unique:titles,name',
            ],
            'reward_title_icon' => ['nullable', 'string', 'max:10'],
        ];
    }

    /** @return array<string, string> */
    protected function rewardTitleMessages(): array
    {
        return [
            'reward_title_name.required' => 'Informe o nome do título que será concedido.',
            'reward_title_name.unique' => 'Já existe um título com este nome.',
            'reward_title_name.max' => 'O nome do título pode ter no máximo 60 caracteres.',
        ];
    }

    /** @return array{name: string, icon: ?string}|null */
    public function rewardTitle(): ?array
    {
        if (! $this->boolean('award_title')) {
            return null;
        }

        return [
            'name' => $this->validated('reward_title_name'),
            'icon' => $this->validated('reward_title_icon'),
        ];
    }
}
