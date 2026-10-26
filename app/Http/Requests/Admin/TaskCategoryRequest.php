<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskCategoryRequest extends FormRequest
{
    private const HEX_COLOR = '/^#[0-9a-fA-F]{6}$/';

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', Rule::unique('task_categories', 'name')->ignore($this->route('category'))],
            'base_points' => ['required', 'integer', 'min:0'],
            'color' => ['required', 'string', 'regex:'.self::HEX_COLOR],
            'text_color' => ['required', 'string', 'regex:'.self::HEX_COLOR],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'base_points' => 'pontos base',
            'color' => 'cor da categoria',
            'text_color' => 'cor da fonte',
        ];
    }
}
