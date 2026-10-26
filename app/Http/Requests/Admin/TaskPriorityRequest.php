<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Valida criação e edição de gravidade; na edição ignora o nome da própria gravidade. */
class TaskPriorityRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', Rule::unique('task_priorities', 'name')->ignore($this->route('priority'))],
            'multiplier' => ['required', 'numeric', 'min:0'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['name' => 'nome', 'multiplier' => 'multiplicador'];
    }
}
