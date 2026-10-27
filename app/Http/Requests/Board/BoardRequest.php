<?php

namespace App\Http\Requests\Board;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BoardRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('boards', 'name')->ignore($this->route('board'))],
            'description' => ['nullable', 'string'],
            'github_repository' => ['nullable', 'string', 'max:140', 'regex:/^[\w.\-]+\/[\w.\-]+$/', Rule::unique('boards', 'github_repository')->ignore($this->route('board'))],
            'is_active' => ['boolean'],
            'seed_default_columns' => $this->isMethod('POST') ? ['boolean'] : ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['name' => 'nome', 'description' => 'descrição', 'github_repository' => 'repositório do GitHub', 'is_active' => 'situação'];
    }

    public function withDefaultColumns(): bool
    {
        return $this->boolean('seed_default_columns', true);
    }
}
