<?php

namespace App\Livewire\Concerns;

use Illuminate\Validation\Rule;

trait ValidatesRewardTitle
{
    public bool $award_title = false;

    public string $reward_title_name = '';

    public string $reward_title_icon = 'medal';

    protected function rewardTitleRules(): array
    {
        return [
            'award_title' => ['boolean'],
            'reward_title_name' => [
                Rule::requiredIf($this->award_title),
                'nullable',
                'string',
                'max:60',
                'unique:titles,name',
            ],
            'reward_title_icon' => ['nullable', 'string', 'max:10'],
        ];
    }

    protected function rewardTitleMessages(): array
    {
        return [
            'reward_title_name.required' => 'Informe o nome do título que será concedido.',
            'reward_title_name.unique' => 'Já existe um título com este nome.',
            'reward_title_name.max' => 'O nome do título pode ter no máximo 60 caracteres.',
        ];
    }

    /** @return array{name: string, icon: ?string}|null */
    protected function rewardTitleData(array $validated): ?array
    {
        if (! $this->award_title) {
            return null;
        }

        return [
            'name' => $validated['reward_title_name'],
            'icon' => $validated['reward_title_icon'] ?? null,
        ];
    }
}
