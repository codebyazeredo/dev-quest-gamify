<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class MoveTaskRequest extends FormRequest
{
    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'column_id' => ['required', 'integer', 'exists:board_columns,id'],
            'position' => ['required', 'integer', 'min:0'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['column_id' => 'coluna', 'position' => 'posição'];
    }
}
